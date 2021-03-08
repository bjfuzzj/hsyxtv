<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use App\Models\Order;
use App\Models\OrderLogistics;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderAllExport;
use App\Models\OrderLog;

class BackOrderController extends Controller
{
    //订单列表
    public function index(Request $request)
    {
        $oid_like           = $request->input('oid_like', '');
        $product_title      = $request->input('product_title', '');
        $nick_name          = $request->input('nick_name', '');
        $start_buytime      = $request->input('start_buytime', '');
        $end_buytime        = $request->input('end_buytime', '');
        $payment_begin_time = $request->input('payment_begin_time', '');
        $payment_end_time   = $request->input('payment_end_time', '');
        $status             = $request->input('status', '');
        $size               = $request->input('size', 50);
        $page               = $request->input('page', 1);


        $query = DB::table('orders as os')
            ->join('users as u', 'os.user_id', '=', 'u.id')
            ->join('products as p', 'os.product_id', '=', 'p.id')
            ->leftJoin('order_logistics as ol', function ($query) {
                $query->on('os.id', '=', 'ol.order_id')->whereNull('ol.deleted_at');
            })
            ->selectRaw('os.product_type,os.group_id,os.buy_num,os.created_at,os.paid_time,os.amount_paid,os.amount_coupon,os.amount_refundable,os.amount_refunded,os.status,os.amount_total
            ,p.title,u.id,ol.receiver_name,ol.receiver_province,ol.receiver_city,
            ol.receiver_district,ol.receiver_mobile,ol.receiver_address,ol.express_no,ol.express_name,u.nick_name, u.head_image, os.id as oid, os.status');

        if (!empty($oid_like)) {
            $query->where('os.id', 'like', '%' . $oid_like . '%');
        }
        if (!empty($nick_name)) {
            $query->where('u.nick_name', 'like', '%' . $nick_name . '%');
        }
        if (!empty($product_title)) {
            $query->where('p.title', 'like', '%' . $product_title . '%');
        }
        if (!empty($status)) {
            $query->where('os.status', $status);
        }
        if (!empty($start_buytime)) {
            $query->where('os.created_at', '>=', $start_buytime);
        }
        if (!empty($end_buytime)) {
            $query->where('os.created_at', '<=', $end_buytime);
        }
        if (!empty($payment_begin_time)) {
            $query->where('os.paid_time', '>=', $payment_begin_time);
        }
        if (!empty($payment_end_time)) {
            $query->where('os.paid_time', '<=', $payment_end_time);
        }
        $query->orderBy('os.id','desc');
        $queryRes   = $query->paginate($size, ['*'], 'page', $page);
        $recordList = [];
        foreach ($queryRes->items() as $item) {
            /*
            $secondDistributor = $firstDistributor = [
                'nick_name'  => '',
                'avatar_url' => ''
            ];
            $distributorId     = $item->distributor_id;
            $distributor       = Distributor::find($distributorId);
            if ($distributor instanceof Distributor) {
                //$firstDistributor['avatar_url'] = $distributor->getUser();
                $firstDistributor['nick_name'] = $distributor->name;
            }
            */
            $recordList[] = [
                'oid'               => $item->oid,
                'title'             => $item->title,
                'buy_num'           => $item->buy_num,
                'product_type_desc'      => Product::$typeDesc[$item->product_type],
                'group_id'          => $item->group_id,
                'create_time'       => $item->created_at,
                'paid_time'         => $item->paid_time,
                'name'              => $item->receiver_name,
                'phone'             => $item->receiver_mobile,
                'status_desc'       => Order::$order_status_desc[$item->status],
                'amount_paid'       => $item->amount_paid,
                'amount_total'      => $item->amount_total,
                'amount_coupon'     => $item->amount_coupon,
                'amount_refundable' => $item->amount_refundable,
                'amount_refunded'   => $item->amount_refunded,
                'address'           => $item->receiver_province . $item->receiver_city . $item->receiver_district . $item->receiver_address,
                'express_no'        => $item->express_no,
                'express_name'      => $item->express_name,
                'status'            => $item->status,
                'nick_name'         => $item->nick_name,
            ];
        }
        return $this->outSuccessResult([
            'records'      => $recordList,
            'current_page' => $queryRes->currentPage(),
            'per_page'     => $queryRes->perPage(),
            'total'        => $queryRes->total(),
        ]);
    }


    //订单详情
    public function detail(Request $request)
    {
        $id = $request->input('id', '');
        if (empty($id)) {
            return $this->outErrorResult('510', '订单不存在');
        }
        $order = Order::with(['user', 'logistics' => function ($query) {
            $query->withTrashed();
        }, 'product'])->where('id', $id)->first();
        if (!$order instanceof Order || !$order->product instanceof Product || !$order->user instanceof User) {
            return $this->outErrorResult('511', '订单不存在');
        }
        $orderData = $order->toArray();
        if(empty($orderData)) $orderData['express_no'] = '';
        return $this->outSuccessResult([
            'order' => $orderData,
        ]);
    }

    //导出物流订单模板
    public function exportExcel(Request $request)
    {
        $oid_like           = $request->input('oid_like', '');
        $product_title      = $request->input('product_title', '');
        $nick_name          = $request->input('nick_name', '');
        $start_buytime      = $request->input('start_buytime', '');
        $end_buytime        = $request->input('end_buytime', '');
        $payment_begin_time = $request->input('payment_begin_time', '');
        $payment_end_time   = $request->input('payment_end_time', '');
        $status             = $request->input('status', '');
        $size               = $request->input('size', 1000);
        $page               = $request->input('page', 1);

        $query = DB::table('orders as os')->join('order_logistics as ol', 'os.id', '=', 'ol.order_id')
            ->join('users as u', 'os.user_id', '=', 'u.id')
            ->join('products as p', 'os.product_id', '=', 'p.id')
            ->whereNull('ol.deleted_at')
            ->selectRaw('os.buy_num,os.created_at,os.paid_time,os.amount_paid,os.amount_coupon,os.amount_refundable
            ,os.amount_refunded,os.status,os.amount_total
            ,p.id as product_id,p.price as price,p.title,u.id,ol.receiver_name,ol.receiver_province,ol.receiver_city,
            ol.receiver_district,ol.receiver_mobile,ol.receiver_address,ol.express_no,ol.express_name,ol.quantity,ol.id as logistic_id,
            u.nick_name, u.head_image, os.id as oid, os.status')->where('ol.deleted_at', '=', null);

        if (!empty($oid_like)) {
            $query->where('os.id', 'like', '%' . $oid_like . '%');
        }
        if (!empty($nick_name)) {
            $query->where('u.nick_name', 'like', '%' . $nick_name . '%');
        }
        if (!empty($status)) {
            $query->where('os.status', $status);
        }
        if (!empty($product_title)) {
            $query->where('p.title', 'like', '%' . $product_title . '%');
        }
        if (!empty($start_buytime)) {
            $query->where('os.created_at', '>=', $start_buytime);
        }
        if (!empty($end_buytime)) {
            $query->where('os.created_at', '<=', $end_buytime);
        }
        if (!empty($payment_begin_time)) {
            $query->where('os.paid_time', '>=', $payment_begin_time);
        }
        if (!empty($payment_end_time)) {
            $query->where('os.paid_time', '<=', $payment_end_time);
        }
        $queryRes   = $query->paginate($size, ['*'], 'page', $page);
        $recordList = [];
        foreach ($queryRes->items() as $item) {
            //for ($foo = 1; $foo <= $item->buy_num; $foo++) {
            for ($foo = 1; $foo <= $item->quantity; $foo++) {
                $recordList[] = [
                    'oid'               => "'{$item->oid}'",
                    'title'             => $item->title,
                    'buy_num'           => $item->buy_num,
                    'create_time'       => $item->created_at,
                    'paid_time'         => $item->paid_time,
                    'name'              => $item->receiver_name,
                    'phone'             => $item->receiver_mobile,
                    'product_id'        => $item->product_id,
                    'status_desc'       => Order::$order_status_desc[$item->status] ?? '',
                    'amount_paid'       => $item->amount_paid,
                    'amount_total'      => $item->amount_total,
                    'amount_coupon'     => $item->amount_coupon,
                    'amount_refundable' => $item->amount_refundable,
                    'amount_refunded'   => $item->amount_refunded,
                    'address'           => $item->receiver_province . $item->receiver_city . $item->receiver_district . $item->receiver_address,
                    'express_no'        => $item->express_no,
                    'express_name'      => $item->express_name,
                    'price'             => $item->price,
                    'quantity'          => $item->quantity,
                    'beizhu'            => $foo . '/' . $item->quantity,
                    'logistic_no'       => $item->oid . '_' . $item->logistic_id,
                ];
            }
        }
        $fileName = '订单物流记录-' . time() . '.xlsx';
        return Excel::download(new OrderExport($recordList), $fileName);
    }


    //导出订单信息
    public function exportOrderExcel(Request $request)
    {
        $oid_like           = $request->input('oid_like', '');
        $product_title      = $request->input('product_title', '');
        $nick_name          = $request->input('nick_name', '');
        $start_buytime      = $request->input('start_buytime', '');
        $end_buytime        = $request->input('end_buytime', '');
        $payment_begin_time = $request->input('payment_begin_time', '');
        $payment_end_time   = $request->input('payment_end_time', '');
        $status             = $request->input('status', '');
        $size               = $request->input('size', 1000);
        $page               = $request->input('page', 1);

        $query = DB::table('orders as os')
            ->join('users as u', 'os.user_id', '=', 'u.id')
            ->join('products as p', 'os.product_id', '=', 'p.id')
            ->leftJoin('order_logistics as ol', function ($query) {
                $query->on('os.id', '=', 'ol.order_id')->whereNull('ol.deleted_at');
            })
            ->selectRaw('ol.receiver_name,ol.receiver_province,ol.receiver_city,ol.receiver_district,ol.receiver_address,ol.receiver_mobile, 
            os.id as oid,p.title,p.id as pid,os.buy_num,p.price as price,os.amount_paid,u.nick_name
            ,os.created_at,os.paid_time,os.status,u.id,ol.express_no,ol.express_name,os.adminnote,os.user_id, os.remark');


        //->join('distributor_details as dd', 'os.id', '=', 'dd.order_id')
        if (!empty($oid_like)) {
            $query->where('os.id', 'like', '%' . $oid_like . '%');
        }
        if (!empty($nick_name)) {
            $query->where('u.nick_name', 'like', '%' . $nick_name . '%');
        }
        if (!empty($status)) {
            $query->where('os.status', $status);
        }
        if (!empty($product_title)) {
            $query->where('p.title', 'like', '%' . $product_title . '%');
        }
        if (!empty($start_buytime)) {
            $query->where('os.created_at', '>=', $start_buytime);
        }
        if (!empty($end_buytime)) {
            $query->where('os.created_at', '<=', $end_buytime);
        }
        if (!empty($payment_begin_time)) {
            $query->where('os.paid_time', '>=', $payment_begin_time);
        }
        if (!empty($payment_end_time)) {
            $query->where('os.paid_time', '<=', $payment_end_time);
        }
        $queryRes = $query->paginate($size, ['*'], 'page', $page);

        $orderIds = [];
        foreach ($queryRes->items() as $item) {
            $orderIds[] = $item->oid;
        }
        //获取返利数据
        $queryOrders = DB::table('orders as os')
            ->join('distributor_details as dd', 'os.id', '=', 'dd.order_id')
            ->join('users as u', 'dd.user_id', '=', 'u.id')
            ->selectRaw('dd.*, u.nick_name as parent_name')->whereIn('os.id', $orderIds)->get();

        $orderRebates = [];
        foreach ($queryOrders as $distributor_details) {
            //自购
            if ($distributor_details->source_user_id == $distributor_details->user_id) {
                $orderRebates[$distributor_details->order_id]['zigou_mount'] = $distributor_details->rebate_amount;
            } //分销
            else {
                $orderRebates[$distributor_details->order_id]['yiji_mount'] = $distributor_details->rebate_amount;
                $orderRebates[$distributor_details->order_id]['yiji_name']  = $distributor_details->parent_name;
            }
        }

        $recordList = [];
        foreach ($queryRes->items() as $item) {
            $recordList[] = [
                'oid'          => "'{$item->oid}",
                'title'        => $item->title,
                'product_id'   => $item->pid,
                'buy_num'      => $item->buy_num,
                'create_time'  => $item->created_at,
                'paid_time'    => $item->paid_time,
                'name'         => $item->receiver_name,
                'phone'        => $item->receiver_mobile,
                'status_desc'  => Order::$order_status_desc[$item->status] ?? '',
                'amount_paid'  => $item->amount_paid,
                'address'      => $item->receiver_province . $item->receiver_city . $item->receiver_district . $item->receiver_address,
                'express_no'   => $item->express_no,
                'express_name' => $item->express_name,
                'price'        => $item->price,
                'adminnote'    => $item->adminnote,
                'remark'       => $item->remark,
                'nick_name'    => $item->nick_name,
                'yiji_mount'   => $orderRebates[$item->oid]['yiji_mount'] ?? '',
                'yiji_name'    => $orderRebates[$item->oid]['yiji_name'] ?? '',
                'zigou_mount'  => $orderRebates[$item->oid]['zigou_mount'] ?? '',
            ];
        }


        $fileName = '订单记录-' . time() . '.xlsx';
        return Excel::download(new OrderAllExport($recordList), $fileName);
    }

    public function markSend(Request $request)
    {
        $params = $this->validate($request, [
            'oid'    => 'integer',
            'status' => 'required|in:' . implode(',', array_keys(Order::$order_status_desc)),
        ], [
            '.*' => '操作失败[-1]',
        ]);
        $order  = Order::find($params['oid']);
        if (!$order instanceof Order) {
            return $this->outErrorResult(-1, '操作失败[-2]');
        }
        if (false == $order->markSend()) {
            return $this->outErrorResult(-1, '操作失败[-3]');
        }
        return $this->outSuccessResult(['id' => $order->id, 'status' => $order->status]);
    }


    public function doSave(Request $request)
    {
        Log::info(__CLASS__ . '_' . __FUNCTION__ . ' operId:' . $request->user()->id, $request->all());
        $orderId      = $request->input('id');
        $adminNote    = $request->input('adminnote', '');
        $amountCoupon  = $request->input('amount_coupon', 0);
        $logisticList = $request->input('logistics', []);

        $order = Order::find($orderId);
        if (!$order instanceof Order) {
            return $this->outErrorResult(-1, '操作失败[-2]');
        }
        $log = '订单id:'.$order->id.'修改优惠券价格由'.$order->amount_coupon.'改成'. $amountCoupon;
        Log::info(__CLASS__ . '_' . __FUNCTION__ . ' operId:' . $request->user()->user_name. $log);
        if($order->changeCoupon($amountCoupon)){
            OrderLog::addLog($order->id, $log, OrderLog::TYPE_ADMIN, $request->user()->id, $request->user()->user_name);
        }
        //先判断发货数量
        $readyToSendNum = 0;
        foreach ($logisticList as $logistics) {
            $logistics = json_decode($logistics, true);
            if (empty($logistics)) {
                continue;
            }
            $readyToSendNum += (int) $logistics['quantity'];
            if ($readyToSendNum > $order->buy_num) {
                return $this->outErrorResult(-1, '发货数超过订单数，请修改后重试[-1]');
            }
        }

        foreach ($logisticList as $logistics) {
            $logistics = json_decode($logistics, true);
            if (empty($logistics)) {
                continue;
            }
            if (empty($logistics['id'])) {
                if (
                    empty($logistics['receiver_name']) || empty($logistics['receiver_mobile']) || empty($logistics['receiver_province'])
                    || empty($logistics['receiver_city']) || empty($logistics['receiver_address'])
                ) {
                    continue;
                }
                $orderLogistics = OrderLogistics::add($orderId, $logistics);
            } else {
                $orderLogistics = OrderLogistics::find($logistics['id']);
                if (!$orderLogistics instanceof OrderLogistics) {
                    continue;
                }
                $orderLogistics->updateData($logistics);
            }
        }
        //超过数量的也不自动标记发货
        // $sentTotal = OrderLogistics::where('order_id', $order->id)->where('status', OrderLogistics::STATUS_SENT)->sum('quantity');
        // if ($sentTotal >= $order->buy_num) {
        //     $order->markSend();
        // }

        if (!$order->modifyAdminNote($adminNote)) {
            return $this->outErrorResult(-1, '操作失败[-3]');
        }
        return $this->outSuccessResult([
            'id' => $order->id
        ]);
    }


    public function markNoSend(Request $request)
    {
        $params = $this->validate($request, [
            'oid'    => 'integer',
            'status' => 'required|in:' . implode(',', array_keys(Order::$order_status_desc)),
        ], [
            '.*' => '操作失败[-1]',
        ]);
        $order  = Order::find($params['oid']);
        if (!$order instanceof Order) {
            return $this->outErrorResult(-1, '操作失败[-2]');
        }
        if (false == $order->markNoSend()) {
            return $this->outErrorResult(-1, '操作失败[-3]');
        }

        return $this->outSuccessResult(['id' => $order->id, 'status' => $order->status]);
    }

    public function doDeleteAddress(Request $request)
    {
        $params = $this->validate($request, [
            'id'    => 'integer',
        ], [
            '.*' => '操作失败[-1]',
        ]);
        $orderLogistic  = OrderLogistics::find($params['id']);
        if (!$orderLogistic instanceof OrderLogistics) {
            return $this->outErrorResult(-1, '操作失败[-2]');
        }
        if (false == $orderLogistic->delete()) {
            return $this->outErrorResult(-1, '操作失败[-3]');
        }
        return $this->outSuccessResult();
    }

    // 申请退款
    public function doRefundByAdmin(Request $request)
    {
        $params = $this->validate($request, [
            'orderId'      => 'required|integer',
            'refundAmount' => 'required|numeric',
        ], [
            'refundAmount.*' => '请输入退款金额',
            '*'              => '申请退款失败[-1]',
        ]);
        Log::info(__CLASS__ . '_' . __FUNCTION__ . ' operId:' . $request->user()->id, $params);
        $orderId      = $params['orderId'];
        $refundAmount = $params['refundAmount'];

        if ($refundAmount <= 0) {
            return $this->outErrorResult(-1, '退款金额必须大于0');
        }
        //$order = Order::where('id', $orderId)->first();
        $order = Order::find($orderId);
        if (!$order instanceof Order) {
            return $this->outErrorResult(-1, '申请退款失败[-1]');
        }

        $result = $order->doRefund($refundAmount, $errorMsg);
        if (!$result) {
            Log::error(__CLASS__ . '_' . __FUNCTION__ . " orderId:{$orderId} errMsg:{$errorMsg}");
            return $this->outErrorResult(-1, $errorMsg ?? '申请退款失败[-2]');
        }

        OrderLog::addLog($order->id, "操作退款：{$refundAmount}元", OrderLog::TYPE_ADMIN, $request->user()->id);
        return $this->outSuccessResult();
    }
}
