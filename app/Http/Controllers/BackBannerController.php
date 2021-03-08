<?php

namespace App\Http\Controllers;


use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BackBannerController extends Controller
{

    public function index(Request $request)
    {
        $params           = $this->validate($request, [
            'page'               => 'nullable|integer',
            'size'               => 'nullable|integer',
        ]);
        $result                   = Banner::getList($params);
        $result['clicktype_options'] = Banner::$clickTypeOptions;
        return $this->outSuccessResult($result);
    }

    public function detail(Request $request){
        $params    = $this->validate($request, [
            'id' => 'nullable|integer',
        ]);
        $bannerId = $params['id'] ?? 0;

        $editData = [];
        $editData['clicktype_options'] = Banner::$clickTypeOptions;
        if (!empty($bannerId)) {
            $editData['banner'] = Banner::find($bannerId)->toArray();
        }
        return $this->outSuccessResult($editData);
    }

    public function doSave(Request $request)
    {
        $params = $this->validate($request, [
            'id'             => 'integer',
            'content'        => 'required|string|min:1',
            'clicktype'      => 'required|in:' . implode(',', array_keys(Banner::$clickTypeDesc)),
            'type'           => 'required|string|min:1|max:60',
            'clickcontent'   => 'nullable|string',
        ], [
            '.*' => '保存数据失败[-1]',
        ]);
        if (empty($params['id'])) {
            $banner = Banner::add($params);
        } else {
            $banner = Banner::find($params['id']);
            if (!$banner instanceof Banner) {
                return $this->outErrorResult(-1, '保存数据失败[-2]');
            }
            $banner->updateData($params);
        }
        if (!$banner instanceof Banner) {
            return $this->outErrorResult(-1, '保存数据失败[-3]');
        }
        return $this->outSuccessResult();
    }


    public function OffLine(Request $request)
    {
        $params = $this->validate($request, [
            'id'     => 'integer',
            'status' => 'required|in:' . implode(',', array_keys(Banner::$statusDesc)),
        ], [
            '.*' => '下线商品失败[-1]',
        ]);
        $banner = Banner::find($params['id']);
        if (!$banner instanceof Banner) {
            return $this->outErrorResult(-1, '下线商品失败[-2]');
        }
        if(!$banner->setOffLine()) {
            return $this->outErrorResult(-1, '下线商品失败[-3]');
        }
        return $this->outSuccessResult(['id'=>$banner->id,'status'=>$banner->status]);
        //add Log


    }

    public function Online(Request $request)
    {
        $params = $this->validate($request, [
            'id'     => 'integer',
            'status' => 'required|in:' . implode(',', array_keys(Banner::$statusDesc)),
        ], [
            '.*' => '上线Banner失败[-1]',
        ]);
        $banner = Banner::find($params['id']);
        if (!$banner instanceof Banner) {
            return $this->outErrorResult(-1, '上线Banner失败[-2]');
        }
        if(!$banner->setOnLine()) {
            return $this->outErrorResult(-1, '上线Banner失败[-3]');
        }
        return $this->outSuccessResult(['id'=>$banner->id,'status'=>$banner->status]);
    }

}
