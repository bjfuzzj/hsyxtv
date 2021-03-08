<?php

namespace App\Http\Controllers;

use App\Helper\CacheKey;
use App\Helper\Codec;
use App\Http\Resources\DistributorIncomeResource;
use App\Models\CashOutRecord;
use App\Models\DistributorDetail;
use App\Models\DistributorIncome;
use App\Models\User;
use App\Models\WxUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IncomeController extends Controller
{

    /**
     * @api {get} /api/income/my 我的收入
     * @apiName myIncome
     * @apiGroup Income
     *
     * @apiParam {String} user_token 用户Token
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {String} data.has_income 是否存在收入 0:不存在 1:存在
     * @apiSuccess {Object} data.status_options 状态筛选项
     * @apiSuccess {Number} data.status_options.value 状态值
     * @apiSuccess {String} data.status_options.label 状态描述
     *
     * @apiSuccess {Object} data.income_data 收入金额
     * @apiSuccess {String} data.income_data.income_total 累计收入
     * @apiSuccess {String} data.income_data.income_used 已提现收入
     * @apiSuccess {String} data.income_data.income_usable 可提现收入
     * @apiSuccess {String} data.income_data.income_freeze 待结算收入
     *
     * @apiSuccess {Object} data.income_detail 收入详情
     * @apiSuccess {Number} data.income_detail.page 当前第几页
     * @apiSuccess {Number} data.income_detail.has_more 是否存在更多数据 0:不存在 1:存在
     * @apiSuccess {Array} data.income_detail.list 收入详情
     * @apiSuccess {String} data.income_detail.list.product_title 产品标题
     * @apiSuccess {String} data.income_detail.list.amount_paid 支付金额
     * @apiSuccess {Number} data.income_detail.list.month 月份
     * @apiSuccess {String} data.income_detail.list.order_time 下单时间
     * @apiSuccess {Number} data.income_detail.list.buy_num 购买数量
     * @apiSuccess {String} data.income_detail.list.rebate_amount 返利金额
     * @apiSuccess {String} data.income_detail.list.status_desc 状态
     * @apiSuccess {String} data.income_detail.list.invalid_reason 失效原因
     * @apiSuccess {String} data.income_detail.list.receiver_name 购买人姓名
     * @apiSuccess {String} data.income_detail.list.receiver_mobile 购买人手机号
     * @throws \Illuminate\Validation\ValidationException
     */
    public function myIncome(Request $request)
    {
        $params = $this->validate($request, [
            'user_token' => 'required|string',
        ], [
            '*' => '加载数据失败[-1]'
        ]);
        $userId = Codec::decodeId($params['user_token']);

        $income = DistributorIncome::where('user_id', $userId)->first();
        // if (!$income instanceof DistributorIncome) {
        //     return $this->outErrorResultApi(500, '加载数据失败[-2]');
        // }
        $hasIncome = 0;
        $incomeData = [];
        if($income instanceof DistributorIncome){
            if($income->income_total > 0) {
                $hasIncome = 1;
            }
            $incomeData = DistributorIncomeResource::make($income);
        }

        $incomeList = DistributorDetail::getList($userId);
        return $this->outSuccessResultApi([
            'has_income'     => $hasIncome,
            'status_options' => DistributorDetail::$statusOptions,
            'income_data'    => $incomeData,
            'income_detail'  => $incomeList
        ]);
    }

    /**
     * @api {get} /api/income/details 收入详情列表
     * @apiName getDetailList
     * @apiGroup Income
     *
     * @apiParam {String} user_token 用户Token
     * @apiParam {Number} status 收入状态
     * @apiParam {Number} page 当前第几页
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     *
     * @apiSuccess {Object} data.income_detail 收入详情
     * @apiSuccess {Number} data.income_detail.page 当前第几页
     * @apiSuccess {Number} data.income_detail.has_more 是否存在更多数据 0:不存在 1:存在
     * @apiSuccess {Array} data.income_detail.list 收入详情
     * @apiSuccess {String} data.income_detail.list.product_title 产品标题
     * @apiSuccess {String} data.income_detail.list.amount_paid 支付金额
     * @apiSuccess {Number} data.income_detail.list.month 月份
     * @apiSuccess {String} data.income_detail.list.order_time 下单时间
     * @apiSuccess {Number} data.income_detail.list.buy_num 购买数量
     * @apiSuccess {String} data.income_detail.list.rebate_amount 返利金额
     * @apiSuccess {String} data.income_detail.list.status_desc 状态
     * @apiSuccess {String} data.income_detail.list.invalid_reason 失效原因
     * @apiSuccess {String} data.income_detail.list.receiver_name 购买人姓名
     * @apiSuccess {String} data.income_detail.list.receiver_mobile 购买人手机号
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getDetailList(Request $request)
    {
        $params         = $this->validate($request, [
            'user_token' => 'required|string',
            'status'     => 'required|integer',
            'page'       => 'nullable|integer',
        ], [
            '*' => '加载数据失败[-1]'
        ]);
        $userId         = Codec::decodeId($params['user_token']);
        $params['size'] = 20;

        $result = DistributorDetail::getList($userId, $params);
        return $this->outSuccessResultApi($result);
    }

    /**
     * @api {get} /api/income/show_cashout 提现页面数据
     * @apiName showCashOut
     * @apiGroup Income
     *
     * @apiParam {String} user_token 用户Token
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {String} data.income_usable 可提现金额
     * @apiSuccess {Array} data.intro 提现说明
     * @throws \Illuminate\Validation\ValidationException
     */
    public function showCashOut(Request $request)
    {
        $params = $this->validate($request, [
            'user_token' => 'required|string',
        ], [
            '*' => '加载数据失败[-1]'
        ]);
        $userId = Codec::decodeId($params['user_token']);

        $user = User::find($userId);
        if (!$user instanceof User) {
            return $this->outErrorResultApi(500, '加载数据失败[-2]');
        }

        $income = DistributorIncome::where('user_id', $userId)->first();
        if (!$income instanceof DistributorIncome) {
            return $this->outErrorResultApi(500, '加载数据失败[-3]');
        }

        $isNeedAuth = 0;
        if (empty($user->real_name) || empty($user->mobile) || empty($user->id_card)) {
            $isNeedAuth = 1;
        }

        return $this->outSuccessResultApi([
            'is_need_auth'  => $isNeedAuth,
            'auth_tip'      => '姓名、手机号、身份证号用于缴纳个人劳务报酬所得税，不作他用，请您放心填写。',
            'income_usable' => $income->income_usable,
            'intro'         => array(
                '1、到账时间：提交申请后，预计收入将在2个工作日内到账；',
                '2、到账通知：提现到微信后，到账将收到微信官方服务提醒通知；',
                '3、个税说明：本月累计提现金额超过800，按税务局要求需交劳务报酬所得税，具体计算公式请参考百度百科：劳务报酬所得；',
                '4、特别说明：泡泡伴侣不会要求您进行转账验证等可能泄露您个人信息的操作。'
            )
        ]);
    }

    /**
     * @api {post} /api/income/cashout 提现
     * @apiName cashOut
     * @apiGroup Income
     *
     * @apiParam {String} _token 校验用户身份Token
     * @apiParam {String} user_token 用户Token
     * @apiParam {Float} amount 提现金额
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {String} data.success_tip 提现成功提示文案
     * @throws \Illuminate\Validation\ValidationException
     */
    public function cashOut(Request $request)
    {
        $params = $this->validate($request, [
            '_token'     => 'required|string',
            'user_token' => 'required|string',
            'amount'     => 'required|numeric',
        ], [
            '*' => '亲，提现失败，请稍后再试[-1]'
        ]);
        $openId = WxUser::getOpenId($params['_token']);
        $userId = Codec::decodeId($params['user_token']);

        $income = DistributorIncome::where('user_id', $userId)->first();
        if (!$income instanceof DistributorIncome) {
            return $this->outErrorResultApi(500, '亲，提现失败，请稍后再试[-2]');
        }

        // 加锁防止重复提交
        $lockKey = CacheKey::CK_DISTRIBUTOR_CASH_OUT_LOCK . $userId;
        if (!Cache::lock($lockKey, 60)->get()) {
            return $this->outErrorResultApi(600, '亲，正在处理你的提现，请稍等片刻。');
        }

        try {
            $rest = $income->cashOut($openId, $params['amount'], $errorMsg);
            if (!$rest) {
                Cache::forget($lockKey);
                return $this->outErrorResultApi(500, $errorMsg ?? '亲，提现失败，请稍后再试[-3]');
            }
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . ':' . __FUNCTION__ . ' 提现失败 error:' . $e->getMessage()
                . ' params:' . print_r($request->all(), 1));
            Cache::forget($lockKey);
            return $this->outErrorResultApi(500, '亲，提现失败，请稍后再试[-4]');
        }
        Cache::forget($lockKey);
        return $this->outSuccessResultApi([
            'success_tip' => '提现成功，预计1-3工作日会转入你的微信余额账户，请关注微信通知。'
        ]);
    }

    /**
     * @api {get} /api/income/cashout_records 提现记录
     * @apiName cashOutRecords
     * @apiGroup Income
     *
     * @apiParam {String} user_token 用户Token
     * @apiParam {Number} page 当前第几页
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     *
     * @apiSuccess {Number} data.page 当前第几页
     * @apiSuccess {Number} data.has_more 是否存在更多数据 0:不存在 1:存在
     * @apiSuccess {Array} data.list 记录列表
     * @apiSuccess {String} data.list.title 标题
     * @apiSuccess {Float} data.list.amount_total 提现金额
     * @apiSuccess {String} data.list.person_taxes_tip 劳务报酬所得税提示
     * @apiSuccess {String} data.list.trade_no 单号
     * @apiSuccess {Number} data.list.status 提现状态 1:待审核 2:提现成功 3:提现失败
     * @apiSuccess {String} data.list.fail_reason 失败原因
     * @apiSuccess {String} data.list.create_time 提现时间
     * @throws \Illuminate\Validation\ValidationException
     */
    public function cashOutRecords(Request $request)
    {
        $params         = $this->validate($request, [
            'user_token' => 'required|string',
            'page'       => 'nullable|integer',
        ], [
            '*' => '亲，加载数据失败，请稍后再试[-1]'
        ]);
        $userId         = Codec::decodeId($params['user_token']);
        $params['size'] = 20;

        $result = CashOutRecord::getCashOutRecordListForApi($userId, $params);
        return $this->outSuccessResultApi($result);
    }

}
