<?php

namespace App\Http\Controllers;

use App\Exports\CashOutExport;
use App\Models\CashOutRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class BackCashOutController extends Controller
{

    public function index(Request $request)
    {
        $params           = $this->validate($request, [
            'nick_name'          => 'nullable|string',
            'trade_no'           => 'nullable|string',
            'payment_no'         => 'nullable|string',
            'status'             => 'nullable|integer',
            'create_begin_date'  => 'nullable|string',
            'create_end_date'    => 'nullable|string',
            'payment_begin_date' => 'nullable|string',
            'payment_end_date'   => 'nullable|string',
            'sort_column'        => 'nullable|string',
            'sort_type'          => 'nullable|string',
            'page'               => 'nullable|integer',
            'size'               => 'nullable|integer',
        ]);
        $params['status'] = $params['status'] ?? CashOutRecord::STATUS_WAIT;

        $result                   = CashOutRecord::getCashOutRecordListForAdmin($params);
        $result['status_options'] = CashOutRecord::$adminStatusOptions;
        return $this->outSuccessResult($result);
    }

    public function exportExcel(Request $request)
    {
        $params           = $this->validate($request, [
            'nick_name'          => 'nullable|string',
            'trade_no'           => 'nullable|string',
            'payment_no'         => 'nullable|string',
            'status'             => 'nullable|integer',
            'create_begin_date'  => 'nullable|string',
            'create_end_date'    => 'nullable|string',
            'payment_begin_date' => 'nullable|string',
            'payment_end_date'   => 'nullable|string',
        ]);
        $params['status'] = $params['status'] ?? CashOutRecord::STATUS_WAIT;
        $params['size']   = 10000;

        $result     = CashOutRecord::getCashOutRecordListForAdmin($params);
        $recordList = $result['list'] ?? [];
        $title      = (CashOutRecord::$cashOutStatus[$params['status']] ?? '提现') . '记录-' . date('Y-m-d') . '.xlsx';
        return Excel::download(new CashOutExport($recordList), $title);
    }

    public function calcPersonTaxes(Request $request)
    {
        $params = $this->validate($request, [
            'user_id'        => 'required|integer',
            'cashout_amount' => 'required|numeric',
        ], [
            '*' => '提现异常，参数错误[-1]',
        ]);

        $taxesInfo = CashOutRecord::calcCashOutTaxes($params['user_id'], $params['cashout_amount']);
        return $this->outSuccessResult($taxesInfo);
    }

    public function cashOut(Request $request)
    {
        $params = $this->validate($request, [
            'id' => 'required|integer',
        ], [
            '*' => '提现失败，参数异常[-1]',
        ]);
        Log::info(__CLASS__ . '_' . __FUNCTION__, $params);
        $auditorId   = $request->user()->id;
        $auditorName = $request->user()->real_name;

        $record = CashOutRecord::find($params['id']);
        if (!$record instanceof CashOutRecord) {
            return $this->outErrorResult(-1, '提现失败[-2]');
        }
        if ($record->isSuccess()) {
            return $this->outSuccessResult();
        }
        if (!$record->isWaitCashout()) {
            return $this->outErrorResult(-1, '提现失败[-3]');
        }

        $rest = $record->cashOut($auditorId, $auditorName, $errMsg);
        if (!$rest) {
            return $this->outErrorResult(-1, $errMsg ?: '提现失败[-4]');
        }
        return $this->outSuccessResult();
    }

    public function refuseCashOut(Request $request)
    {
        $params = $this->validate($request, [
            'id'            => 'required|integer',
            'refuse_reason' => 'nullable|string',
        ], [
            '*' => '提交拒绝失败，参数异常[-1]',
        ]);
        Log::info(__CLASS__ . '_' . __FUNCTION__, $params);
        $auditorId   = $request->user()->id;
        $auditorName = $request->user()->real_name;

        $record = CashOutRecord::find($params['id']);
        if (!$record instanceof CashOutRecord) {
            return $this->outErrorResult(-1, '提现记录数据异常，操作失败[-2]');
        }
        if ($record->isFail()) {
            return $this->outSuccessResult();
        }
        if (!$record->isWaitCashout()) {
            return $this->outErrorResult(-1, '提现记录数据异常，操作失败[-3]');
        }

        $rest = $record->doRefuse($params['refuse_reason'] ?? '', $auditorId, $auditorName);
        if (!$rest) {
            return $this->outErrorResult(-1, '提现记录数据异常，操作失败[-4]');
        }
        return $this->outSuccessResult();
    }

}
