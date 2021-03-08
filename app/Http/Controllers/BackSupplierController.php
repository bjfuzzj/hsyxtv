<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class BackSupplierController extends Controller
{
    public function index(Request $request)
    {
        $supplierName = $request->input('name', '');
        $size         = $request->input('size', 50);
        $page         = $request->input('page', 1);

        if (!empty($supplierName)) {
            $queryRes = Supplier::where('name', 'like', '%' . $supplierName . '%')->paginate($size, ['*'], 'page', $page);
        } else {
            $queryRes = Supplier::paginate($size, ['*'], 'page', $page);
        }
        $recordList = [];
        foreach ($queryRes as $item) {
            $recordList[] = [
                'id'               => $item->id,
                'name'             => $item->name,
                'phone'            => $item->phone,
                'email'            => $item->email,
                'settle_type_desc' => Supplier::$settle_type_desc[$item->settlement_type],
                'deal_type_desc'   => Supplier::$deal_type_desc[$item->deal_type],
                'create_time'      => $item->create_time,
                'qrcode'           => $item->qrcode
            ];
        }
        return $this->outSuccessResult([
            'records'      => $recordList,
            'current_page' => $queryRes->currentPage(),
            'per_page'     => $queryRes->perPage(),
            'total'        => $queryRes->total(),
        ]);
    }


    public function add(Request $request)
    {
        try {
            $params = $this->validate($request, [
                'name'            => 'required|string',
                'phone'           => 'string',
                'email'           => 'string',
                'settlement_type' => 'required|in:' . implode(',', array_keys(Supplier::$settle_type_desc)),
                'deal_type'       => 'required|in:' . implode(',', array_keys(Supplier::$deal_type_desc))
            ]);
        } catch (ValidationException $e) {
            Log::error(__CLASS__ . '_' . __FUNCTION__ . ' 参数错误 params:' . print_r($request->all(), 1)
                . ' errMsg:' . $e->getMessage());
            return $this->outErrorResult(-1, '参数错误');
        }
        $supplier                  = new Supplier();
        $supplier->name            = $params['name'];
        $supplier->phone           = $params['phone'] ?? '';
        $supplier->email           = $params['email'] ?? '';
        $supplier->settlement_type = $params['settlement_type'];
        $supplier->deal_type       = $params['deal_type'];
        $result                    = $supplier->save();
        if ($result) {
            return $this->outSuccessResult([], '添加成功');
        }
        return $this->outErrorResult(-1, '增加失败');
    }

    public function doUpdate(Request $request)
    {
        try {
            $params = $this->validate($request, [
                'id'              => 'required|integer',
                'name'            => 'required|string',
                'phone'           => 'string',
                'email'           => 'string',
                'settlement_type' => 'required|in:' . implode(',', array_keys(Supplier::$settle_type_desc)),
                'deal_type'       => 'required|in:' . implode(',', array_keys(Supplier::$deal_type_desc))
            ]);
        } catch (ValidationException $e) {
            Log::error(__CLASS__ . '_' . __FUNCTION__ . ' 参数错误 params:' . print_r($request->all(), 1)
                . ' errMsg:' . $e->getMessage());
            return $this->outErrorResult(-1, '参数错误');
        }
        $supplier = Supplier::where('id', $params['id'])->update($params);
        return $this->outSuccessResult([], '更新成功');

    }


    public function detail(Request $request)
    {
        $id       = $request->input('id', 0);
        $supplier = Supplier::find($id);
        if (false == $supplier instanceof Supplier) {
            return $this->outErrorResult(500, '供应商不存在');
        }

        return $this->outSuccessResult(
            [
                'supplier' => $supplier
            ]
        );


    }

    public function getSelectOptions()
    {
        $supplierOptions = Supplier::get(['name', 'id'])->toArray();
        return $this->outSuccessResult([
            'options' => $supplierOptions
        ]);
    }
}
