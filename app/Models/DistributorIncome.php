<?php
/*
 * @Author: your name
 * @Date: 2020-12-02 18:06:10
 * @LastEditTime: 2020-12-07 21:01:51
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Models/DistributorIncome.php
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 分销员收入
 * Class DistributorIncome
 * @package App
 *
 * @property int id
 * @property int user_id 用户ID
 * @property float income_total 累计收入
 * @property float income_used 已提现的收入
 * @property float income_usable 可提现的收入
 * @property float income_freeze 待结算的收入
 * @property float person_taxes 个人所得税
 * @property string created_at 创建时间
 * @property string updated_at 更新时间
 */
class DistributorIncome extends Model
{
    protected $table = 'distributor_incomes';

    // 提现金额范围，单位元
    const CASH_OUT_AMOUNT_MIN = 10;
    const CASH_OUT_AMOUNT_MAX = 5000;

    // 每天最多可提现10次（微信规则）
    const CASH_OUT_MAX_NUM = 10;

    public static function add($userId)
    {
        $self          = new self();
        $self->user_id = $userId;
        $self->save();
        return $self;
    }
    
    // 添加收入
    public function addIncome($amount)
    {
        $this->increment('income_total', $amount);
        $this->increment('income_freeze', $amount);
    }

    public static function addIncomeByAdvanceSale(Order $order, $isUpgrade)
    {
        $user = User::find($order->user_id);
        if (!$user instanceof User) {
            Log::error(__CLASS__ . '_' . __FUNCTION__ . ' 添加收入失败，用户不存在');
            return false;
        }

        // 预售用户上级返利
        $parentId = UserRelationship::where('user_id', $user->id)->value('parent_id');
        if (!empty($parentId)) {
            $parentRebatePercentage = User::ADVANCE_SALE_DISTRIBUTOR_PERCENTAGE;
            DistributorDetail::addDetail($order, $parentId, $parentRebatePercentage,
                DistributorDetail::SOURCE_SELF_DISTRIBUTOR, $user->id);
        }

        // 预售返利
        $selfRebatePercentage = User::$advanceSaleRebatePercentage[$user->advance_sale_level] ?? 0;
        if ($selfRebatePercentage > 0) {
            $advanceSaleProductIds = Product::where('type', Product::TYPE_ADVANCE)->pluck('id')->toArray();
            $orderList             = Order::whereIn('product_id', $advanceSaleProductIds)->where('user_id', $user->id)
                ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_SHIPPED, Order::STATUS_RECEIVED])->get();

            foreach ($orderList as $orderItem) {
                $detail = DistributorDetail::where('order_id', $orderItem->id)->where('user_id', $user->id)->first();
                if (!$detail instanceof DistributorDetail) {
                    DistributorDetail::addDetail($orderItem, $user->id, $selfRebatePercentage,
                        DistributorDetail::SOURCE_SELF_DISTRIBUTOR, $user->id);
                } else if ($isUpgrade) {
                    $detail->updateRebatePercentage($orderItem, $selfRebatePercentage);
                }
            }
        }
    }

    public static function addIncomeByNormalSale(Order $order)
    {
        $product = Product::withTrashed()->find($order->product_id);
        // 上级用户返利
        $parentId = UserRelationship::where('user_id', $order->user_id)->value('parent_id');
        if (!empty($parentId)) {
            $parentUser = User::find($parentId);
            if ($parentUser instanceof User) {
                //$parentRebatePercentage = User::$distributorRebatePercentage[$parentUser->distributor_level] ?? 0;
                $amount = $parentUser->getRebateAmount(1,$product);
                //$amount = $product->first_rebate_amount;
                DistributorDetail::addDetailByAmount($order, $parentId, $amount,
                    DistributorDetail::SOURCE_SELF_DISTRIBUTOR, $parentId);
            }
        }

        // 上上级用户返利
        $superiorId = UserRelationship::where('user_id', $parentId)->value('parent_id');
        if (!empty($parentId) && !empty($superiorId)) {
            $superiorUser = User::find($superiorId);
            if ($superiorUser instanceof User) {
                //$superiorRebatePercentage = User::SUPERIOR_DISTRIBUTOR_PERCENTAGE;
                $amount = $parentUser->getRebateAmount(2,$product);
                //$amount = $product->second_rebate_amount;
                DistributorDetail::addDetailByAmount($order, $superiorId, $amount,
                    DistributorDetail::SOURCE_SUBORDINATE_DISTRIBUTOR, $parentId);
            }
        }
    }
    
    // 提现
    public function cashOut($openId, $amount, &$errorMsg)
    {
        $log = "id:{$this->id} user_id:{$this->user_id} amount:{$amount}";
        Log::info("开始提现 $log");

        $user = User::find($this->user_id);
        if (!$user instanceof User) {
            Log::error("提现 $log errorMsg:分销员用户不存在");
            $errorMsg = '亲，提现失败，请稍后重试[1]';
            return false;
        }
        if ($amount < self::CASH_OUT_AMOUNT_MIN) {
            Log::error("提现 $log errorMsg:提现金额不能小于" . self::CASH_OUT_AMOUNT_MIN);
            $errorMsg = '提现金额不能小于' . self::CASH_OUT_AMOUNT_MIN . '元';
            return false;
        }
        if ($amount > self::CASH_OUT_AMOUNT_MAX) {
            Log::error("提现 $log errorMsg:提现金额大于" . self::CASH_OUT_AMOUNT_MAX);
            $errorMsg = '单次最多可提现' . self::CASH_OUT_AMOUNT_MAX . '元';
            return false;
        }
        if ($amount > $this->income_usable) {
            Log::error("提现 $log errorMsg:提现金额大于可提现金额" . $this->income_usable);
            $errorMsg = '最多可提现' . $this->income_usable . '元';
            return false;
        }

        $today    = date('Y-m-d');
        $todayNum = CashOutRecord::where('user_id', $this->user_id)
            ->where(function ($query) use ($today) {
                $query->where('created_at', '>=', $today)->orWhere('payment_time', '>=', $today);
            })
            ->count();
        if ($todayNum >= self::CASH_OUT_MAX_NUM) {
            Log::error("提现 $log errorMsg:超出每天提现" . self::CASH_OUT_MAX_NUM . '次');
            $errorMsg = '亲，微信规定每人每天最多提现' . self::CASH_OUT_MAX_NUM . '次，请明天再来提现吧。';
            return false;
        }

        DB::beginTransaction();
        try {
            CashOutRecord::add($openId, $user->id, $amount);

            $this->increment('income_used', $amount);
            $this->decrement('income_usable', $amount);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("提现 $log 更新数据失败 errorMsg:" . $e->getMessage());
            $errorMsg = '提现失败，请稍后重试[3]';
            return false;
        }
        return true;
    }
}
