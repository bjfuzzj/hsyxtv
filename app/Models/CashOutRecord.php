<?php

namespace App\Models;

use App\Helper\PushMessage;
use App\Helper\WxPayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 提现记录
 * Class CashOutRecord
 * @package App
 *
 * @property int id
 * @property int user_id 用户ID
 * @property float amount_total 提现金额
 * @property float amount_payment 真实支付金额
 * @property float person_taxes 个人所得税
 * @property string open_id 用户openid
 * @property string trade_no 提现商户订单号
 * @property string payment_no 微信付款单号
 * @property int status 状态 1:待审核 2:提现成功 3:提现失败
 * @property string fail_reason 失败原因
 * @property string payment_time 微信支付交易时间
 * @property int auditor_id 审核员ID
 * @property string auditor_name 审核员名称
 * @property string audit_time 审核时间
 * @property string created_at 创建时间
 * @property string updated_at 更新时间
 */
class CashOutRecord extends Model
{
    protected $table = 'cashout_records';

    // 提现状态 1:待审核 2:提现成功 3:提现失败
    const STATUS_WAIT    = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_FAIL    = 3;
    public static $cashOutStatus = [
        self::STATUS_WAIT    => '提现审批中',
        self::STATUS_SUCCESS => '提现成功',
        self::STATUS_FAIL    => '提现失败',
    ];
    public static $adminStatusOptions = [
        ['value' => self::STATUS_WAIT, 'label' => '提现审批中'],
        ['value' => self::STATUS_SUCCESS, 'label' => '提现成功'],
        ['value' => self::STATUS_FAIL, 'label' => '提现失败'],
    ];

    // 每天最多可提现10次（微信规则）
    const CASH_OUT_MAX_NUM = 10;

    // 每天最多可提现金额（微信规则）
    const CASH_OUT_AMOUNT_MAX = 5000;

    public static function add($openId, $userId, $amount)
    {
        $self               = new self();
        $self->user_id      = $userId;
        $self->amount_total = $amount;
        $self->open_id      = $openId;
        $self->trade_no     = uniqid($userId, false);
        $self->status       = self::STATUS_WAIT;
        $self->save();
        return $self;
    }

    // public static function getCashOutRecordList($userId, $params)
    // {
    //     $page = $params['page'] ?? 1;
    //     $size = $params['size'] ?? 20;

    //     $queryRes = self::where('user_id', $userId)->orderByDesc('id')->simplePaginate($size);

    //     $recordList = [];
    //     foreach ($queryRes->items() as $item) {
    //         $recordList[] = [
    //             'title'            => '平台推广佣金提现',
    //             'amount_total'     => $item->amount_total,
    //             'person_taxes_tip' => $item->person_taxes > 0 ? '（劳务报酬所得税扣除￥' . $item->person_taxes . '）' : '',
    //             'trade_no'         => $item->trade_no,
    //             'status'           => $item->status,
    //             'fail_reason'      => $item->fail_reason,
    //             'payment_time'     => Carbon::parse($item->payment_time)->format('Y-m-d H:i'),
    //             'create_time'      => $item->created_at->format('Y-m-d H:i')
    //         ];
    //     }
    //     return [
    //         'list'     => $recordList,
    //         'has_more' => $queryRes->hasMorePages() ? 1 : 0,
    //         'page'     => (int)$page
    //     ];
    // }


    public static function getCashOutRecordListForAdmin($params)
    {
        $sortColumn = $params['sort_column'] ?? 'id';
        $sortType   = $params['sort_type'] ?? 'desc';
        $size       = $params['size'] ?? 20;

        $query = DB::table('cashout_records as cr')
            ->join('users as u', 'cr.user_id', '=', 'u.id')
            ->selectRaw('u.nick_name, u.real_name, u.mobile, u.id_card, u.head_image,  cr.*');
        if (!empty($params['status'])) {
            $query->where('cr.status', $params['status']);
        }
        if (!empty($params['nick_name'])) {
            $query->where(function ($query) use ($params) {
                $query->where('u.nick_name', 'like', "%{$params['nick_name']}%")
                    ->orWhere('u.real_name', 'like', "%{$params['nick_name']}%");
            });
        }
        if (!empty($params['trade_no'])) {
            $query->where('cr.trade_no', 'like', "%{$params['trade_no']}%");
        }
        if (!empty($params['payment_no'])) {
            $query->where('cr.payment_no', 'like', "%{$params['payment_no']}%");
        }
        if (!empty($params['create_begin_date'])) {
            $query->where('cr.created_at', '>=', $params['create_begin_date']);
        }
        if (!empty($params['create_end_date'])) {
            $query->where('cr.created_at', '<=', $params['create_end_date'] . ' 23:59:59');
        }
        if (!empty($params['payment_begin_date'])) {
            $query->where('cr.payment_time', '>=', $params['payment_begin_date']);
        }
        if (!empty($params['payment_end_date'])) {
            $query->where('cr.payment_time', '>=', $params['payment_end_date'] . ' 23:59:59');
        }
        $queryRes = $query->orderBy("cr.{$sortColumn}", $sortType)->paginate($size);

        $list = [];
        foreach ($queryRes->items() as $item) {
            $itemArr                = collect($item)->toArray();
            $itemArr['status_desc'] = self::$cashOutStatus[$item->status] ?? '';
            $list[]                 = $itemArr;
        }

        return [
            'list'         => $list,
            'current_page' => $queryRes->currentPage(),
            'total'        => $queryRes->total(),
        ];
    }

    // 计算提现金额劳务报酬所得税
    public static function calcCashOutTaxes($userId, $cashOutAmount)
    {
        $startDate  = date('Y-m-01');
        $endDate    = today()->endOfMonth()->toDateTimeString();
        $recordList = self::where('user_id', $userId)->where('payment_time', '>', $startDate)
            ->where('status', self::STATUS_SUCCESS)->where('payment_time', '<', $endDate)
            ->get(['amount_total', 'person_taxes']);

        $alreadyAmount = 0;
        $alreadyTaxes  = 0;
        foreach ($recordList as $record) {
            $alreadyAmount = round($alreadyAmount + $record->amount_total, 2);
            $alreadyTaxes  = round($alreadyTaxes + $record->person_taxes, 2);
        }
        $totalAmount = round($alreadyAmount + $cashOutAmount, 2);
        if ($totalAmount <= 800) {
            return [
                'already_cashout' => $alreadyAmount,
                'already_taxes'   => $alreadyTaxes,
                'taxes'           => 0
            ];
        }

        if ($totalAmount <= 4000) {
            $taxableAmount = round($totalAmount - 800, 2);
        } else {
            $taxableAmount = round($totalAmount * 0.8, 2);
        }

        if ($taxableAmount <= 20000) {
            $totalTaxes = round($taxableAmount * 0.2, 2);
        } else if ($taxableAmount <= 50000) {
            $totalTaxes = round($taxableAmount * 0.3 - 2000, 2);
        } else {
            $totalTaxes = round($taxableAmount * 0.4 - 7000, 2);
        }
        $taxes = round($totalTaxes - $alreadyTaxes, 2);
        return [
            'already_cashout' => $alreadyAmount,
            'already_taxes'   => $alreadyTaxes,
            'taxes'           => $taxes
        ];
    }

    public function cashOut($auditorId, $auditorName, &$errorMsg)
    {
        $log = "id:{$this->id} user_id:{$this->user_id} amount:{$this->amount_total}";
        Log::info("开始提现 $log");

        $taxesInfo     = self::calcCashOutTaxes($this->user_id, $this->amount_total);
        $personTaxes   = $taxesInfo['taxes'];
        $amountPayment = round($this->amount_total - $personTaxes, 2);

        $user = User::find($this->user_id);
        if (!$user instanceof User) {
            Log::error("提现 $log errorMsg:分销员用户不存在");
            $errorMsg = '提现失败，请稍后重试[1]';
            return false;
        }

        $today    = date('Y-m-d');
        $todayNum = self::where('user_id', $this->user_id)->where('payment_time', '>=', $today)->count();
        if ($todayNum >= self::CASH_OUT_MAX_NUM) {
            Log::error("提现 $log errorMsg:超出每天提现" . self::CASH_OUT_MAX_NUM . '次');
            $errorMsg = '亲，微信规定每天每人最多提现' . self::CASH_OUT_MAX_NUM . '次，请明天再来提现吧。';
            return false;
        }
        $todayAmount = self::where('user_id', $this->user_id)->where('payment_time', '>=', $today)->sum('amount_payment');
        if (round($todayAmount + $amountPayment, 2) > self::CASH_OUT_AMOUNT_MAX) {
            Log::error("提现 $log errorMsg:提现金额大于可提现金额" . self::CASH_OUT_AMOUNT_MAX);
            $errorMsg = '因为微信规则，每天最多可提现' . self::CASH_OUT_AMOUNT_MAX . '元';
            return false;
        }

        $feeAmount = round($amountPayment * 100);
        $result    = WxPayment::getInstance()->transferToBalance($this->trade_no, $this->open_id, $feeAmount, '佣金提现');
        if (!isset($result['return_code']) || WxPayment::RETURN_SUCCESS != $result['return_code']
            || !isset($result['result_code']) || WxPayment::RESULT_SUCCESS != $result['result_code']) {
            Log::error("提现 $log 发起提现失败 result:" . print_r($result, 1));
            $errorMsg = $result['err_code_des'] ?? '提现失败，请稍后重试[3]';
            return false;
        }

        $this->status         = self::STATUS_SUCCESS;
        $this->amount_payment = $amountPayment;
        $this->person_taxes   = $personTaxes;
        $this->payment_no     = $result['payment_no'] ?? '';
        $this->payment_time   = $result['payment_time'] ?? '';
        $this->auditor_id     = $auditorId;
        $this->auditor_name   = $auditorName;
        $this->audit_time     = now()->toDateTimeString();
        $this->save();

        Log::info("提现完成 $log");
        $this->sendCashOutSuccessMsg();
        return true;
    }

    // 发送支付成功消息
    private function sendCashOutSuccessMsg()
    {
        try {
            $wxUser = WxUser::where('user_id', $this->user_id)->where('source', WxUser::SOURCE_WXAPP)->first();
            if ($wxUser instanceof WxUser) {
                $page   = PushMessage::PAGE_USER_INCOME;
                $amount = '￥' . $this->amount_total;
                $status = '提现成功';
                $type   = "收入{$this->amount_payment}元已存入您的微信余额，请查收。";
                if ($this->person_taxes > 0) {
                    $type = "您本月累计提现金额超过800，按税务局要求需交劳务报酬所得税，本次提现平台已代缴代扣{$this->person_taxes}元个税，税后收入{$this->amount_payment}元已存入您的微信余额，请查收。";
                }
                PushMessage::sendCashOutSuccessNotice($wxUser->open_id, $page, $amount, $status, $type);
            }
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . '_' . __FUNCTION__ . ' 发消息异常 orderId:' . $this->id . ' errMsg:' . $e->getMessage());
        }
    }

    public function doRefuse($reason, $auditorId = 0, $auditorName = 0)
    {
        $income = DistributorIncome::where('user_id', $this->user_id)->first();
        if (!$income instanceof DistributorIncome) {
            Log::error("拒绝提现 用户收入不存在 id:{$this->id} params:" . print_r(\func_get_args(), 1));
            return false;
        }
        DB::beginTransaction();
        try {
            $this->status       = self::STATUS_FAIL;
            $this->fail_reason  = $reason;
            $this->auditor_id   = $auditorId;
            $this->auditor_name = $auditorName;
            $this->audit_time   = now()->toDateTimeString();
            $this->save();

            $income->increment('income_usable', $this->amount_total);
            $income->decrement('income_used', $this->amount_total);

            $this->sendCashOutFailMsg();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("拒绝提现 更新数据失败 id:{$this->id} errorMsg:{$e->getMessage()} params:" . print_r(\func_get_args(), 1));
            return false;
        }
        return true;
    }

    // 发送支付成功消息
    private function sendCashOutFailMsg()
    {
        try {
            $wxUser = WxUser::where('user_id', $this->user_id)->where('source', WxUser::SOURCE_WXAPP)->first();
            if ($wxUser instanceof WxUser) {
                $page = PushMessage::PAGE_USER_INCOME;
                $amount = '￥' . $this->amount_total;
                $desc = '提现失败，提现金额已返还。';
                if (!empty($this->fail_reason)) {
                    $desc .= "失败原因：{$this->fail_reason}";
                }
                PushMessage::sendCashOutFailNotice($wxUser->open_id, $page, $amount, $desc);
            }
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . '_' . __FUNCTION__ . ' 发消息异常 orderId:' . $this->id . ' errMsg:' . $e->getMessage());
        }
    }

    public function isWaitCashout()
    {
        return self::STATUS_WAIT == $this->status;
    }

    public function isSuccess()
    {
        return self::STATUS_SUCCESS == $this->status;
    }

    public function isFail()
    {
        return self::STATUS_FAIL == $this->status;
    }


    public static function getCashOutRecordListForApi($userId, $params)
    {
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? 20;

        $queryRes = self::where('user_id', $userId)->orderByDesc('id')->simplePaginate($size);

        $recordList = [];
        foreach ($queryRes->items() as $item) {
            $recordList[] = [
                'title'            => '平台推广佣金提现',
                'amount_total'     => $item->amount_total,
                'person_taxes_tip' => $item->person_taxes > 0 ? '（劳务报酬所得税扣除￥' . $item->person_taxes . '）' : '',
                'trade_no'         => $item->trade_no,
                'status'           => $item->status,
                'fail_reason'      => $item->fail_reason,
                'payment_time'     => Carbon::parse($item->payment_time)->format('Y-m-d H:i'),
                'create_time'      => $item->created_at->format('Y-m-d H:i')
            ];
        }
        return [
            'list'     => $recordList,
            'has_more' => $queryRes->hasMorePages() ? 1 : 0,
            'page'     => (int)$page
        ];
    }

}
