<?php

namespace App\Imports;

use App\Models\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\OrderLogistics;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithStartRow;

class OrderImport implements ToCollection, WithStartRow
{

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            $logisticNo  = !empty($row[0]) ? trim($row[0]) : '';
            $expressNo   = !empty($row[1]) ? trim($row[1]) : '';
            $expressName = !empty($row[2]) ? trim($row[2]) : '';

            $logisticNoArr = explode('_', $logisticNo);
            if (empty($logisticNoArr[0]) || empty($logisticNoArr[1])) {
                Log::error("数据异常 第{$key}行 logisticNo:{$logisticNo} expressNo:{$expressNo} expressName:{$expressName}");
                continue;
            }

            list($orderId, $logisticId) = $logisticNoArr;
            if (empty($logisticNo) || empty($orderId) || empty($expressNo) || empty($expressName)) {
                Log::error("数据异常 第{$key}行 logisticId:{$logisticId} orderId:{$orderId} expressNo:{$expressNo} expressName:{$expressName}");
                continue;
            }

            $orderLogistics = OrderLogistics::find($logisticId);
            if (!$orderLogistics instanceof OrderLogistics) {
                Log::error("物流数据不存在 第{$key}行 logisticId:{$logisticId} orderId:{$orderId} expressNo:{$expressNo} expressName:{$expressName}");
                continue;
            }
            $order = Order::find($orderId);
            if (!$order instanceof Order || $order->id != $orderLogistics->order_id) {
                Log::error("订单不存在 第{$key}行 logisticId:{$logisticId} orderId:{$orderId} expressNo:{$expressNo} expressName:{$expressName}");
                continue;
            }

            $expressNoList = explode(',', $orderLogistics->express_no);
            if (\in_array($expressNo, $expressNoList)) {
                continue;
            }
            $expressNoList[] = $expressNo;

            $expressNameList = explode(',', $orderLogistics->express_name);
            if (!in_array($expressName, $expressNameList)) {
                $expressNameList[] = $expressName;
            }

            if (count($expressNoList) == $orderLogistics->quantity) {
                $orderLogistics->status = OrderLogistics::STATUS_SENT;
            } else {
                $orderLogistics->status = OrderLogistics::STATUS_PART_SENT;
            }

            $expressNoList   = array_filter($expressNoList);
            $expressNoList   = array_unique($expressNoList);
            $expressNameList = array_filter($expressNameList);
            $expressNameList = array_unique($expressNameList);

            $orderLogistics->express_no   = implode(',', $expressNoList);
            $orderLogistics->express_name = implode(',', $expressNameList);
            $orderLogistics->save();

            // 判断订单是否全部发货
            $allExpressCount = 0;
            $expressNos      = OrderLogistics::where('order_id', $order->id)->pluck('express_no')->toArray();
            foreach ($expressNos as $expressNo) {
                $allExpressCount += substr_count($expressNo, ',') + 1;
            }
            if ($allExpressCount == $order->buy_num) {
                $order->markSend();
            }
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
