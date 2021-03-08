<?php

namespace App\Models;

use App\Helper\AliSms;
use App\Helper\PushMessage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 分销员收入详情
 * Class DistributorDetail
 * @package App
 *
 * @property int id
 * @property int user_id 用户ID
 * @property int order_id 订单ID
 * @property int product_id 产品ID
 * @property int buy_num 用户购买数量
 * @property float rebate_percentage 返利百分比
 * @property float rebate_amount 返利金额
 * @property float income_usable 可提现的收入
 * @property float single_rebate_amount 单个产品返利金额
 * @property int status 状态 1:待结算 2:可提现 3:失效
 * @property string invalid_reason 失效原因
 * @property int source 来源 1:来源自己推广 2:来源下级分销员推广
 * @property int source_user_id 来源下级分销员用户ID
 * @property string created_at 创建时间
 * @property string updated_at 更新时间
 */
class DistributorDetail extends Model
{
    protected $table = 'distributor_details';

    //状态 1:待结算 2:可提现 3:失效
    const STATUS_WAIT_SETTLE = 1;
    const STATUS_SETTLED     = 2;
    const STATUS_INVALID     = 3;
    public static $statusDesc = [
        self::STATUS_WAIT_SETTLE => '待结算',
        self::STATUS_SETTLED     => '可提现',
        self::STATUS_INVALID     => '失效',
    ];
    public static $statusOptions = [
        ['value' => 0, 'label' => '全部收入'],
        ['value' => self::STATUS_SETTLED, 'label' => '可提现'],
        ['value' => self::STATUS_WAIT_SETTLE, 'label' => '待结算'],
    ];
// 来源 1:来源自己推广 2:来源下级分销员推广
    const SOURCE_SELF_DISTRIBUTOR        = 1;
    const SOURCE_SUBORDINATE_DISTRIBUTOR = 2;
    
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public static function addDetail(Order $order, $userId, $rebatePercentage, $sourceType, $sourceUserId)
    {
        DB::beginTransaction();
        try {
            $self                       = new self();
            $self->user_id              = $userId;
            $self->order_id             = $order->id;
            $self->product_id           = $order->product_id;
            $self->buy_num              = $order->buy_num;
            $self->rebate_percentage    = $rebatePercentage;
            $self->rebate_amount        = round($order->amount_total * $rebatePercentage / 100, 2);
            $self->single_rebate_amount = round($self->rebate_amount / $order->buy_num, 2);
            $self->source               = $sourceType;
            $self->source_user_id       = $sourceUserId;
            $self->status               = self::STATUS_WAIT_SETTLE;
            $self->save();

            $income = DistributorIncome::where('user_id', $userId)->first();
            if (!$income instanceof DistributorIncome) {
                $income = DistributorIncome::add($userId);
            }
            $income->addIncome($self->rebate_amount);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error(__CLASS__ . '_' . __FUNCTION__ . '添加收入记录失败 errMsg:' . $e->getMessage()
                . ' data:', \func_get_args());
            return null;
        }

        $self->sendDistributorSuccess();
        return $self;
    }


    public static function addDetailByAmount(Order $order, $userId, $rebateAmount, $sourceType, $sourceUserId)
    {
        DB::beginTransaction();
        try {
            $self                       = new self();
            $self->user_id              = $userId;
            $self->order_id             = $order->id;
            $self->product_id           = $order->product_id;
            $self->buy_num              = $order->buy_num;
            //$self->rebate_percentage    = $rebatePercentage;
            //$self->rebate_amount        = round($order->amount_total * $rebatePercentage / 100, 2);
            //$self->single_rebate_amount = round($self->rebate_amount / $order->buy_num, 2);
            $self->rebate_percentage    = round($rebateAmount / $order->amount_total / $order->buy_num * 100, 2);
            $self->rebate_amount        = $rebateAmount * $order->buy_num;
            $self->single_rebate_amount = $rebateAmount;
            $self->source               = $sourceType;
            $self->source_user_id       = $sourceUserId;
            $self->status               = self::STATUS_WAIT_SETTLE;
            $self->save();

            $income = DistributorIncome::where('user_id', $userId)->first();
            if (!$income instanceof DistributorIncome) {
                $income = DistributorIncome::add($userId);
            }
            $income->addIncome($self->rebate_amount);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error(__CLASS__ . '_' . __FUNCTION__ . '添加收入记录失败 errMsg:' . $e->getMessage()
                . ' data:', \func_get_args());
            return null;
        }

        //$self->sendDistributorSuccess();
        return $self;
    }

    // 发送分享成功提醒
    private function sendDistributorSuccess()
    {
        try {
            $user = User::find($this->user_id);
            if ($user instanceof User && !empty($user->mobile) && $this->rebate_amount > 0) {
                AliSms::getInstance()->sendAccountChange($user->mobile, $this->rebate_amount);
            }
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . '_' . __FUNCTION__ . ' 发消息异常 orderId:' . $this->id . ' errMsg:' . $e->getMessage());
        }
    }

    public function updateRebatePercentage(Order $order, $rebatePercentage)
    {
        $rebateAmount = round($order->amount_total * $rebatePercentage / 100, 2);
        $diffAmount   = round($rebateAmount - $this->rebate_amount, 2);

        DB::beginTransaction();
        try {
            $this->rebate_percentage    = $rebatePercentage;
            $this->rebate_amount        = round($order->amount_total * $rebatePercentage / 100, 2);
            $this->single_rebate_amount = round($this->rebate_amount / $order->buy_num, 2);
            $this->save();

            $income = DistributorIncome::where('user_id', $this->user_id)->first();
            if ($income instanceof DistributorIncome) {
                $income->addIncome($diffAmount);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error(__CLASS__ . '_' . __FUNCTION__ . '修改收入记录失败 errMsg:' . $e->getMessage()
                . ' data:', \func_get_args());
            return false;
        }
        return true;
    }

    public function settled()
    {
        if (self::STATUS_WAIT_SETTLE != $this->status) {
            return false;
        }

        DB::beginTransaction();
        try {
            $this->status = self::STATUS_SETTLED;
            $this->save();

            $income = DistributorIncome::where('user_id', $this->user_id)->first();
            if ($income instanceof DistributorIncome) {
                $income->increment('income_usable', $this->rebate_amount);
                $income->decrement('income_freeze', $this->rebate_amount);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error(__CLASS__ . '_' . __FUNCTION__ . '结算失败 errMsg:' . $e->getMessage()
                . ' data:', \func_get_args());
            return false;
        }
        return true;
    }
    
    public static function getList($userId, array $params = [])
    {
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? 20;

        $query = DB::table('distributor_details as d')
            ->join('products as p', 'p.id', '=', 'd.product_id')
            ->join('orders as o', 'o.id', '=', 'd.order_id')
            ->join('order_logistics as ol', 'o.id', '=', 'ol.order_id')
            ->selectRaw('p.title as product_title, o.amount_paid, o.paid_time as order_time, d.buy_num, 
                d.rebate_amount, d.status, d.invalid_reason, ol.receiver_name, ol.receiver_mobile')
            ->where('d.user_id', $userId)
            ->orderByDesc('d.id');
        if (!empty($params['status'])) {
            $query->where('d.status', $params['status']);
        }
        $queryRes = $query->simplePaginate($size);

        $incomeList = [];
        foreach ($queryRes->items() as $row) {
            $item = collect($row)->toArray();

            $orderTime           = Carbon::parse($row->order_time);
            $item['month']       = $orderTime->format('m');
            $item['order_time']  = $orderTime->format('Y-m-d H:i');
            $item['amount_paid'] = round($row->amount_paid, 2);
            $item['status_desc'] = self::$statusDesc[$row->status] ?? '';
            if (!empty($row->receiver_mobile)) {
                $item['receiver_mobile'] = substr_replace($row->receiver_mobile, '****', 3, 4);
            }
            unset($item['status']);
            $incomeList[] = $item;
        }

        return array(
            'list'     => $incomeList,
            'page'     => $page,
            'has_more' => $queryRes->hasMorePages() ? 1 : 0
        );
    }

    public static function setInvalidByOrder($orderId, $invalidReason)
    {
        $rewardList = self::where('order_id', $orderId)->where('status', '<>', self::STATUS_INVALID)->get();
        foreach ($rewardList as $reward) {
            $isWaitSettleStatus = ($reward->isWaitSettle() && $reward->isSettled()) ? 1 : 0;

            $reward->status         = self::STATUS_INVALID;
            $reward->invalid_reason = $invalidReason;
            $reward->save();

            if ($isWaitSettleStatus) {
                $distributorIncome = DistributorIncome::where('user_id', $reward->user_id)->first();
                if ($distributorIncome instanceof DistributorIncome) {
                    $distributorIncome->decrement('income_total', $reward->rebate_amount);
                    $distributorIncome->decrement('income_freeze', $reward->rebate_amount);
                }
            }
        }
        return true;
    }


    public static function recalculateByOrder(Order $order)
    {
        $rewardList = self::where('order_id', $order->id)->get();
        if ($rewardList->isEmpty()) {
            return true;
        }

        DB::beginTransaction();
        try {
            foreach ($rewardList as $reward) {
                $reward->rebate_amount        = round(($order->amount_paid - $order->amount_refundable) * $reward->rebatePercentage / 100, 2);
                $reward->single_rebate_amount = round($reward->rebate_amount / $order->buy_num, 2);
                $reward->save();

                if ($reward->isWaitSettle()) {
                    $distributorIncome = DistributorIncome::where('user_id', $reward->user_id)->first();
                    if ($distributorIncome instanceof DistributorIncome) {
                        $distributorIncome->decrement('income_total', $reward->rebate_amount);
                        $distributorIncome->decrement('income_freeze', $reward->rebate_amount);
                    }
                }
            }
            
            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error(__CLASS__ . __FUNCTION__ . ' 重新计算奖励失败 orderId:' . $order->id . 'errMsg:' . $e->getMessage());
            return false;
        }
    }


   

    public function isWaitSettle()
    {
        return self::STATUS_WAIT_SETTLE == $this->status;
    }

    public function isSettled()
    {
        return self::STATUS_SETTLED == $this->status;
    }

    public function isInvalid()
    {
        return self::STATUS_INVALID == $this->status;
    }

}
