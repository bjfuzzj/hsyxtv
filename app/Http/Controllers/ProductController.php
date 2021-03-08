<?php

namespace App\Http\Controllers;

use App\Helper\CacheKey;
use App\Helper\Codec;
use App\Helper\MakePoster;
use App\Helper\MiniProgram;
use App\Models\Banner;
use App\Models\User;
use App\Models\Product;
use App\Models\WxUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\CouponTemplate;
use App\Models\Coupon;
use App\Models\Group;
use App\Models\Activity;
use Illuminate\Support\Carbon;

class ProductController extends Controller
{

    /**
     * @api {get} /api/product/index 产品列表
     * @apiName index
     * @apiGroup Product
     *
     * @apiParam {String} _token 用户身份token
     * @apiParam {String} share_product_token 分享产品
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {String} data.product_token 产品Token
     * @apiSuccess {String} data.title 产品标题
     * @apiSuccess {Float} data.price 产品价格
     * @apiSuccess {Float} data.original_price 产品原价
     * @apiSuccess {Float} data.rebate_amount 返利金额
     * @apiSuccess {String} data.main_img 产品主图
     * @apiSuccess {Number} data.sold_num 销量
     * @apiSuccess {Number} data.is_sell_out 是否售罄 0:否 1:是
     * @apiSuccess {String} data.share_title 分享标题
     * @apiSuccess {String} data.share_image 分享图片
     * @apiSuccess {String} data.show_type 展示类型 1:预售 2:分享 3:正常用户
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request)
    {
        $params = $this->validate($request, [
            '_token'              => 'required|string',
            'share_product_token' => 'nullable|string',
        ], [
            '*' => '加载产品数据失败[-1]'
        ]);
        $openId = WxUser::getOpenId($params['_token']);
        $wxUser = WxUser::where('open_id', $openId)->first();
        if (!$wxUser instanceof WxUser) {
            return $this->outErrorResultApi(500, '加载产品数据失败[-2]');
        }

        $shareProductId = 0;
        if (!empty($params['share_product_token'])) {
            $shareProductId = Codec::decodeId($params['share_product_token']);
        }

        $rebatePercentage = 0;
        $user             = User::find($wxUser->user_id);
        if ($user instanceof User && $user->is_distributor) {
            $rebatePercentage = $user->getSpreadPercentage();
        }

        //$productList = Product::getProductList($user, $wxUser->nick_name, $shareProductId, $rebatePercentage);
        $productList = Product::getProductList($user, $wxUser->nick_name, $shareProductId, $rebatePercentage);

        
         //平台优惠券露出
         $showCoupon = false;
         $now        = now()->toDateTimeString();
         $couponTemplateId = 0;
         $couponSrc = '';


        // if ($user instanceof User && $user->is_distributor) {
        //     $couponTemplate = CouponTemplate::where('status', CouponTemplate::STATUS_ONLINE)->where('receive_end_time', '>=', $now)->where('is_show',1)->first();
        //     if($couponTemplate instanceof CouponTemplate && !empty($wxUser->user_id)){
        //          $coupon = Coupon::where('user_id',$wxUser->user_id)->where('template_id',$couponTemplate->id)->first();
        //          if(false === $coupon instanceof Coupon){
        //              $showCoupon       = true;
        //              $couponSrc        = $couponTemplate->src ?? 'https://img.66gou8.com/ae1df5a1c53c7172b7059a80cd4aac99.png';
        //              $couponTemplateId = $couponTemplate->id;
        //          }
        //      }
        // }

            $couponTemplate = CouponTemplate::where('status', CouponTemplate::STATUS_ONLINE)->where('receive_end_time', '>=', $now)->where('is_show',1)->first();
            if($couponTemplate instanceof CouponTemplate){
                $showCoupon       = true;
                $couponSrc        = $couponTemplate->src ?? 'https://img.66gou8.com/ae1df5a1c53c7172b7059a80cd4aac99.png';
                $couponTemplateId = $couponTemplate->id;
             }
        
       

        $banners = Banner::where('status',Banner::STATUS_ONLINE)->where('type','首页banner')->get();
        $dingBuBanner = [];
        foreach($banners as $banner){
            if($banner['clicktype'] == Banner::CLICK_TYPE_4 || $banner['clicktype'] == Banner::CLICK_TYPE_5)
            {
                $clickcontent = Codec::encodeId($banner['clickcontent']);
            }else{
                $clickcontent = $banner['clickcontent'];
            }
            $singleBanner = [];
            $singleBanner['image_url'] = $banner['content'];
            $singleBanner['type'] = $banner['clicktype'];
            $singleBanner['link'] = $clickcontent;
            $dingBuBanner[] = $singleBanner;
        }
        /*
        //banner相关;  0 无点击效果  1 点击跳转图片   2 点击跳转链接
        //dingBuBanner  suiShouBanner  jiDi
        $dingBuBanner = [
            [
                //'image_url' => 'https://img.etonggan.com/6d77623f5e2471d821692a83737060cd.jpeg',
                'image_url' => 'https://img.etonggan.com/ee8441ccc87b03732214610d36bfc4d9.jpeg',
                'type'      => 1,
                'link'      => 'https://img.etonggan.com/8c8eb4edc1e8d1776967f208de6613d2.jpeg'
            ],
//            [
//                'image_url' => 'https://img.etonggan.com/df74c9215a0b8906dd924188a8bcfc64.jpg',
//                'type'      => 0,
//                'link'      => ''
//            ],
            [
                'image_url' => 'https://img.etonggan.com/c29708e5812d4ad878bbf6c5f2e1fb3c.jpg',
                'type'      => 0,
                'link'      => ''
            ],
        ];
        */
        $suiShouBanner=$jiDi=[];
        // $suiShouBanner = [
        //     [
        //         'image_url' => 'https://img.etonggan.com/d697619f572e41e77d5edc34af5edd53.jpg',
        //         'type'      => 1,
        //         'link'      => 'https://img.etonggan.com/467191583c08cc21f8a3c9210f87f13e.jpg'
        //     ]

        // ];

        // $jiDi = [
        //     [
        //         'image_url' => 'https://img.etonggan.com/def96c6af1d643a2038a2751cc5210ee.jpg',
        //         'type'      => 2,
        //         'link'      => 'https://mp.weixin.qq.com/s/DLwJvjo9VH2jvBAbkfhYxg'
        //     ]
        // ];

        return $this->outSuccessResultApi([
            'list'             => $productList,
            'dingBuBanner'     => $dingBuBanner,
            'suiShouBanner'    => $suiShouBanner,
            'jiDi'             => $jiDi,
            'showCoupon'       => $showCoupon,
            'couponTemplateId' => $couponTemplateId,
            'couponSrc'        => $couponSrc
        ]);
    }

    /**
     * @api {get} /api/product/detail 产品详情
     * @apiName getDetail
     * @apiGroup Product
     *
     * @apiParam {String} _token 用户身份token
     * @apiParam {String} product_token 产品token
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {String} data.product_token 产品Token
     * @apiSuccess {String} data.title 产品标题
     * @apiSuccess {Float} data.price 产品价格
     * @apiSuccess {Float} data.original_price 产品原价
     * @apiSuccess {Float} data.rebate_amount 返利金额
     * @apiSuccess {Array} data.images 产品轮播图
     * @apiSuccess {Array} data.detail_images 产品详情图
     * @apiSuccess {Number} data.sold_num 销量
     * @apiSuccess {Array} data.tags 标签
     * @apiSuccess {String} data.advance_tip 预售结束提醒
     * @apiSuccess {Array} data.customer_tip 咨询客服提示文案
     * @apiSuccess {Array} data.customer_wechat 客服微信号
     * @apiSuccess {Number} data.is_sell_out 是否售罄 1:是 0:不是
     * @apiSuccess {String} data.share_title 分享标题
     * @apiSuccess {String} data.share_image 分享图片
     * @apiSuccess {String} data.show_type 展示类型 1:预售 2:分享 3:正常用户
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getDetail(Request $request)
    {
        $params    = $this->validate($request, [
            '_token'        => 'required|string',
            'product_token' => 'required|string',
        ], [
            '*' => '加载产品数据失败[-1]'
        ]);
        $productId = Codec::decodeId($params['product_token']);
        $openId    = WxUser::getOpenId($params['_token']);
        $product   = Product::find($productId);
        if (!$product instanceof Product) {
            return $this->outErrorResultApi(500, '加载产品数据失败[-2]');
        }
        $wxUser = WxUser::where('open_id', $openId)->first();
        if (!$wxUser instanceof WxUser) {
            return $this->outErrorResultApi(500, '加载产品数据失败[-3]');
        }

        $rebatePercentage = 0;
        $user             = User::find($wxUser->user_id);
        if ($user instanceof User && $user->is_distributor) {
            $rebatePercentage = $user->getSpreadPercentage();
        }

        $productInfo = $product->getDetailData($user,$wxUser->nick_name, $rebatePercentage);

        // 非团购商品可用优惠券
        $productInfo['coupons'] = [];
        if (!$product->isGroupType() && $user instanceof User && $user->is_distributor) {
            $productInfo['coupons'] = CouponTemplate::getProductUsableCoupon($productId, $wxUser->user_id);
        }
        

        //如果是拼团商品
        if($product->isGroupType()){
            $waitingList = [];
            $activity = Activity::where('product_id',$product->id)->first();
            if(!$activity instanceof Activity || $activity->isInValid()){
                $productInfo['is_sell_out'] = 1;
            }else{
                //暂时取原价
                $productInfo['price'] = round($activity->original_price, 2);
                $groupRes = Group::where('activity_id',$activity->id)->where('status',Group::STATUS_ING)->orderByDesc('full_amount')->limit(4)->get();
                foreach($groupRes as $group){
                    $waitingList[] = [
                        'nick_name'  => $group->founder->nick_name ?? '',
                        'head_image' => $group->founder->head_image ?? '',
                        'group_id'   => $group->id,
                        'need_num'   => $activity->full_amount - $group->full_amount,
                        'end_time'   => Carbon::parse($group->end_time)->format(''),
                        //'countdown'  => now()->diffInSeconds($group->end_time),
                        'countdown'  => now()->diffInMilliseconds($group->end_time),
                    ];
                }
            }
            $productInfo['waitingGroups'] = $waitingList;
            $groupNum = Group::where('product_id',$product->id)->sum('full_amount');
            $productInfo['groupNum'] = $groupNum;
        }
        
        return $this->outSuccessResultApi([
            'product' => $productInfo
        ]);
    }

    /**
     * @api {get} /api/product/share_poster 产品分享海报
     * @apiName index
     * @apiGroup Product
     *
     * @apiParam {String} _token 用户身份token
     * @apiParam {String} product_token 分享产品
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {String} data.poster 海报图片链接
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getSharePoster(Request $request)
    {
        $params    = $this->validate($request, [
            '_token'        => 'required|string',
            'product_token' => 'required|string',
        ], [
            '*' => '合成海报失败[-1]'
        ]);
        $productId = Codec::decodeId($params['product_token']);

        $openId = WxUser::getOpenId($params['_token']);
        $wxUser = WxUser::where('open_id', $openId)->first();
        if (!$wxUser instanceof WxUser) {
            return $this->outErrorResultApi(-1, '合成海报失败[-2]');
        }
        $product = Product::find($productId);
        if (!$product instanceof Product) {
            return $this->outErrorResultApi(-1, '合成海报失败[-3]');
        }

        $data     = ['poster' => $product->share_poster, 'nick_name' => $wxUser->nick_name, 'avatar' => $wxUser->avatar];
        $cacheKey = CacheKey::CK_WXAPP_PRODUCT_POSTER . $productId . '_' . $wxUser->id . '_' . md5(json_encode($data));
        $poster   = Cache::get($cacheKey);
        if (!empty($poster)) {
            return $this->outSuccessResultApi(['poster' => $poster]);
        }
        $posterUrl = MakePoster::productWxAppSharePoster($product, $wxUser);
        if (empty($posterUrl)) {
            return $this->outErrorResultApi(-1, '合成海报失败[-4]');
        }

        Cache::put($cacheKey, $posterUrl, 2592000); // 缓存30天
        return $this->outSuccessResultApi(['poster' => $posterUrl]);
    }

    public function test()
    {
    }
}
