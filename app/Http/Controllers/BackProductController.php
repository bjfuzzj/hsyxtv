<?php

namespace App\Http\Controllers;

use App\Helper\Codec;
use App\Helper\MakePoster;
use App\Models\EtcApplyRecord;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductDetail;
use App\Models\ProductSku;
use App\Models\Supplier;
use App\Models\UserRelationship;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Helper\CacheKey;
use App\Helper\MiniProgram;

class BackProductController extends Controller
{

    public function index(Request $request)
    {
        $params = $this->validate($request, [
            'id'     => 'nullable|integer',
            'title'  => 'nullable|string',
            'status' => 'nullable|integer',
            'type'   => 'nullable|integer',
            'page'   => 'nullable|integer',
            'size'   => 'nullable|integer',
        ]);

        $result = Product::getList($params);

        $result['status_options'] = Product::$statusOptions;
        $result['type_options']   = Product::$typeOptions;
        return $this->outSuccessResult($result);
    }

    public function OffLine(Request $request)
    {
        $params = $this->validate($request, [
            'id'     => 'integer',
            'status' => 'required|in:' . implode(',', array_keys(Product::$statusDesc)),
        ], [
            '.*' => '下线商品失败[-1]',
        ]);
        $product = Product::find($params['id']);
        if (!$product instanceof Product) {
            return $this->outErrorResult(-1, '下线商品失败[-2]');
        }
        if(!$product->setOffLine()) {
            return $this->outErrorResult(-1, '下线商品失败[-3]');
        }
        return $this->outSuccessResult(['id'=>$product->id,'status'=>$product->status]);
        //add Log


    }

    public function Online(Request $request)
    {
        $params = $this->validate($request, [
            'id'     => 'integer',
            'status' => 'required|in:' . implode(',', array_keys(Product::$statusDesc)),
        ], [
            '.*' => '发布上线商品失败[-1]',
        ]);
        $product = Product::find($params['id']);
        if (!$product instanceof Product) {
            return $this->outErrorResult(-1, '发布上线商品失败[-2]');
        }
        if(!$product->setOnLine()) {
            return $this->outErrorResult(-1, '发布上线商品失败[-3]');
        }
        return $this->outSuccessResult(['id'=>$product->id,'status'=>$product->status]);
    }

    public function save(Request $request)
    {
        $params = $this->validate($request, [
            'id'                     => 'integer',
            'type'                   => 'required|in:' . implode(',', array_keys(Product::$typeDesc)),
            'title'                  => 'required|string|min:5|max:60',
            'tags'                   => 'nullable|string|min:1|max:60',
            'images'                 => 'required|array|min:1|max:9',
            'detail_images'          => 'required|array|min:1|max:20',
            'rank'                   => 'nullable|integer',
            'price'                  => 'required|numeric',
            'cost'                   => 'required|numeric',
            'original_price'         => 'nullable|numeric',
            'stock'                  => 'required|numeric',
            'share_title'            => 'required|string',
            'share_image'            => 'required|string',
            'share_poster'           => 'required|string',
            'first_rebate_amount'    => 'nullable|numeric',
            'second_rebate_amount'   => 'nullable|numeric',
            's_first_rebate_amount'  => 'nullable|numeric',
            's_second_rebate_amount' => 'nullable|numeric',
            't_first_rebate_amount'  => 'nullable|numeric',
            't_second_rebate_amount' => 'nullable|numeric',
            'buy_min_num'            => 'nullable|numeric',
            'buy_max_num'            => 'nullable|numeric',
            'buy_step'               => 'nullable|numeric',
            'buy_limit_num'          => 'nullable|numeric',
        ], [
            '.*' => '保存商品数据失败[-1]',
        ]);

        if (empty($params['id'])) {
            $product = Product::add($params);
        } else {
            $product = Product::find($params['id']);
            if (!$product instanceof Product) {
                return $this->outErrorResult(-1, '保存商品数据失败[-2]');
            }
            $product->updateData($params);
        }

        if (!$product instanceof Product) {
            return $this->outErrorResult(-1, '保存商品数据失败[-3]');
        }
        return $this->outSuccessResult();
    }

    public function getEditDetail(Request $request)
    {
        $params    = $this->validate($request, [
            'id' => 'nullable|integer',
        ]);
        $productId = $params['id'] ?? 0;

        $editData = [];
        if (!empty($productId)) {
            $editData['product'] = Product::find($productId)->toArray();
            if($editData['product']['tags'] && is_array($editData['product']['tags'])){
                $editData['product']['tags'] = join(',',$editData['product']['tags']);
            }
        }
        return $this->outSuccessResult($editData);
    }

    public function getImage(Request $request)
    {
        $params     = $this->validate($request, [
            'id'     => 'required|integer',
        ], [
            '.*'     => '获取拼团二维码失败[-1]',
        ]);
        $id     = $params['id'];
        $product = Product::find($id);
        if(!$product instanceof Product) {
            return $this->outErrorResult(-1, '获取拼团二维码失败[-2]');
        }

        $url = '';
        $cacheKey = CacheKey::CK_PRODUCT_URL.$product->id; 
        $url   = Cache::get($cacheKey);
        if (empty($url)) {
            if($product->isGroupType()){
                $page = MiniProgram::PAGE_GROUP_DETAIL."?product_token=".Codec::encodeId($product->id); 
                $url = MiniProgram::getInstance()->getWxACode($page, ['width' => 300], true);
            }else{
                $page = MiniProgram::PAGE_PRODUCT_DETAIL."?productID=".Codec::encodeId($product->id); 
                $url = MiniProgram::getInstance()->getWxACode($page, ['width' => 300], true);
            }
            
            Cache::put($cacheKey, $url, 3600*24*30);
        }
        return $this->outSuccessResult([
            'url'=>$url
        ]);
    }


}
