<?php

namespace App\Models;

use App\Helper\Codec;
use App\Helper\Oss;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Class Product
 * @package App\Models
 *
 * @property int id
 * @property string title 产品名称
 * @property string app_source 小程序来源
 * @property int type 产品类型
 * @property string unit 产品单位
 * @property int supplier_id 供应商ID
 * @property string main_img 产品主图
 * @property string images 产品轮播图
 * @property string fee_include 费用包含
 * @property string introduction 使用说明
 * @property string buy_start_time 售卖开始时间
 * @property string buy_end_time 售卖结束时间
 * @property float price 产品销售价格
 * @property float price_original 产品销售原价
 * @property int stock 库存
 * @property string share_title 分享标题
 * @property string share_desc 分享描述
 * @property string wxapp_share_img 小程序分享图
 * @property string h5_share_img h5分享图
 * @property string share_poster 分享海报图
 * @property float first_rebate_amount 分销一级返利
 * @property float second_rebate_amount 分销二级返利
 * @property int is_sku_image sku是否单独设置图片
 * @property int buy_min_num 最低购买数量
 * @property int buy_max_num 最多购买数量
 * @property int buy_step 每次加减数量
 * @property int buy_limit_num 每人限购数量
 * @property string create_time 创建时间
 * @property string edit_time 更新时间
 * @property string deleted_at 删除时间
 */
class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    //产品状态 1:待上架 2:上架 3:下架
    const STATUS_WAIT    = 1;
    const STATUS_ONLINE  = 2;
    const STATUS_OFFLINE = 3;
    public static $statusDesc    = [
        self::STATUS_WAIT    => '待发布',
        self::STATUS_ONLINE  => '正在售卖',
        self::STATUS_OFFLINE => '已下架',
    ];
    //产品状态, 1:待上架, 2:已上架, 3:已下架
    const PRODUCT_STATUS_WAIT    = 1;
    const PRODUCT_STATUS_ONLINE  = 2;
    const PRODUCT_STATUS_OFFLINE = 3;
    public static $product_status_desc = array(
        self::PRODUCT_STATUS_WAIT    => '待上架',
        self::PRODUCT_STATUS_ONLINE  => '已上架',
        self::PRODUCT_STATUS_OFFLINE => '已下架',
    );
    public static $statusOptions = [
        ['status' => self::STATUS_WAIT, 'desc' => '待发布'],
        ['status' => self::STATUS_ONLINE, 'desc' => '正在售卖'],
        ['status' => self::STATUS_OFFLINE, 'desc' => '已下架'],
    ];


    //产品类型 1:预售 2:正常 3:团购商品
    const TYPE_PRESALE = 1;
    const TYPE_ADVANCE = 1;
    const TYPE_NORMAL  = 2;
    const TYPE_GROUP = 3;

    public static $typeDesc    = [
        self::TYPE_PRESALE => '预售商品',
        self::TYPE_NORMAL  => '正常商品',
        self::TYPE_GROUP  => '团购商品'
    ];
    public static $typeOptions = [
        ['type' => self::TYPE_PRESALE, 'desc' => '预售商品'],
        ['type' => self::TYPE_NORMAL, 'desc' => '正常商品'],
        ['type' => self::TYPE_GROUP, 'desc' => '团购商品']
    ];

    const DEFAULT_BUY_MAX_NUM = 99;
    // 无限制购买数量
    const BUY_NUM_UN_LIMIT = -1;

    public function getImagesAttribute($value)
    {
        return !empty($value) ? explode(',', $value) : [];
    }

    public function getDetailImagesAttribute($value)
    {
        return !empty($value) ? explode(',', $value) : [];
    }
    public function getTagsAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }
    public function getMainImage()
    {
        $mainImage = current($this->images);
        if (empty($mainImage)) {
            return '';
        }
        // return Oss::addImageStyle(Oss::STYLE_NAME_W800H800, $mainImage);
        return Oss::addImageStyle('', $mainImage);
    }
    public function getShareTitle($nickName = '')
    {
        $shareTitle = $this->share_title ?: $this->title;
        if (!empty($nickName)) {
            return '@' . $nickName . ' ' . $shareTitle;
        }
        return $shareTitle;
    }

    public static function add($data)
    {
        DB::beginTransaction();
        try {
            $self = new self();
            $self->setData($data);
            $self->save();
            DB::commit();
            return $self;
        } catch (\Throwable $e) {
            DB::rollBack();
            return null;
        }
    }
    // 设置成拼团产品
    public function setGroupType()
    {
        $this->type = self::TYPE_GROUP;
        return $this->save();
        
    }


    // 是否是预售产品
    public function isAdvanceSale()
    {
        return self::TYPE_ADVANCE == $this->type;
    }

    // 是否是预售产品
    public function isNormalSale()
    {
        return self::TYPE_NORMAL == $this->type;
    }

    public function updateData($data)
    {
        DB::beginTransaction();
        try {
            $this->setData($data);
            $this->save();
            DB::commit();
            return $this;
        } catch (\Throwable $e) {
            DB::rollBack();
            return null;
        }
    }

    private function setData($data)
    {
        $this->type                   = $data['type'] ?? self::TYPE_NORMAL;
        $this->title                  = $data['title'] ?? '';
        $this->images                 = !empty($data['images']) ? implode(',', $data['images']) : '';
        $this->detail_images          = !empty($data['detail_images']) ? implode(',', $data['detail_images']) : '';
        $this->price                  = $data['price'] ?? 0;
        $this->tags                   = $data['tags'] ?? '';
        $this->cost                   = $data['cost'] ?? 0;
        $this->rank                   = $data['rank'] ?? 0;
        $this->original_price         = $data['original_price'] ?? 0;
        $this->stock                  = $data['stock'] ?? 0;
        $this->share_title            = $data['share_title'] ?? '';
        $this->share_image            = $data['share_image'] ?? '';
        $this->share_poster           = $data['share_poster'] ?? '';
        $this->first_rebate_amount    = $data['first_rebate_amount'] ?? 0;
        $this->second_rebate_amount   = $data['second_rebate_amount'] ?? 0;
        $this->s_first_rebate_amount  = $data['s_first_rebate_amount'] ?? 0;
        $this->s_second_rebate_amount = $data['s_second_rebate_amount'] ?? 0;
        $this->t_first_rebate_amount  = $data['t_first_rebate_amount'] ?? 0;
        $this->t_second_rebate_amount = $data['t_second_rebate_amount'] ?? 0;
        $this->buy_min_num            = $data['buy_min_num'] ?? 1;
        $this->buy_max_num            = $data['buy_max_num'] ?? self::DEFAULT_BUY_MAX_NUM;
        $this->buy_step               = $data['buy_step'] ?? 1;
        $this->buy_limit_num          = $data['buy_limit_num'] ?? -1;
    }

    private function isWaitOnline()
    {
        return self::STATUS_WAIT == $this->status;
    }

    private function isOnline()
    {
        return self::STATUS_ONLINE == $this->status;
    }

    private function isOffline()
    {
        return self::STATUS_OFFLINE == $this->status;
    }

    public function setOffLine(){
        if($this->isWaitOnline() || $this->isOffline()) {
            return false;
        }
        $this->status = self::STATUS_OFFLINE;
        return $this->save();
    }

    public function setOnLine(){
        if($this->isOnline()) {
            return false;
        }
        $this->status = self::STATUS_ONLINE;
        return $this->save();
    }

    public static function getList($params)
    {
        $size  = $params['size'] ?? 50;
        $query = self::orderByDesc('id');
        if (!empty($params['id'])) {
            $query->where('id', $params['id']);
        }
        if (!empty($params['title'])) {
            $query->where('title', 'like', '%' . $params['title'] . '%');
        }
        if (!empty($params['status'])) {
            $query->where('status', $params['status']);
        }
        if (!empty($params['type'])) {
            $query->where('type', $params['type']);
        }
        $queryRes = $query->paginate($size);

        $productList = [];
        foreach ($queryRes as $item) {
            $productList[] = [
                'id'          => $item->id,
                'title'       => $item->title,
                'type_desc'   => self::$typeDesc[$item->type] ?? '',
                'status_desc' => self::$statusDesc[$item->status] ?? '',
                'stock'       => $item->stock,
                'status'      => $item->status,
                'sold_num'    => $item->sold_num,
                'create_time' => $item->created_at->toDateTimeString(),
                'edit_time'   => $item->updated_at->toDateTimeString(),
            ];
        }
        return [
            'list'         => $productList,
            'current_page' => $queryRes->currentPage(),
            'per_page'     => $queryRes->perPage(),
            'total'        => $queryRes->total(),
        ];

    }

    public function getAdvanceTip()
    {
        if (empty($this->advance_end_time)) {
            return '';
        }
        return '预售' . Carbon::parse($this->advance_end_time)->format('y-m-d H:i') . '结束';
    }

    public function isSellOut()
    {
        return $this->stock <= 0 || self::PRODUCT_STATUS_ONLINE != $this->status;
    }

    public static function getProductList($user, $nickName, $shareProductId = 0, $rebatePercentage = 0)
    {
        $queryRes = self::where('status', self::PRODUCT_STATUS_ONLINE)->orderByDesc('rank')->orderByDesc('id')->get();

        $shareProduct = [];
        $productList  = [];
        foreach ($queryRes as $product) {
            if (!$product instanceof self) {
                continue;
            }
            $price  = $product->price;
            if($product->isGroupType()){
                $activity = Activity::where('product_id',$product->id)->first();
                if($activity instanceof Activity && !$activity->isInValid()) {
                    $price = $activity->original_price;
                }
            }
            $item = [
                'product_token'  => Codec::encodeId($product->id),
                'title'          => $product->title,
                'type'           => $product->type,
                'price'          => round($price, 2),
                'original_price' => round($product->original_price, 2),
                //'rebate_amount'  => round($rebatePercentage * $product->price / 100, 2),
                'main_img'       => $product->getMainImage(),
                'sold_num'       => $product->sold_num,
                'is_sell_out'    => $product->isSellOut() ? 1 : 0,
                'share_title'    => $product->getShareTitle($nickName),
                'share_image'    => Oss::addImageStyle(Oss::STYLE_NAME_W500H400, $product->share_image),
            ];
            if($user instanceof User && $user->isDistributorUser()){
                $item['rebate_amount'] = $user->getRebateAmount(1,$product);
                //$item['show_type'] = $item['rebate_amount'] > 0 ? 2 : 3;
                $item['show_type'] = 2;
            }else{
                $item['rebate_amount'] = 0;
                $item['show_type'] = 3;
            }
            // 展示类型 1:预售 2:分享 3:正常用户
            // if ($product->isAdvanceSale()) {
            //     $item['show_type'] = 1;
            // } else if ($rebatePercentage > 0) {
            //     $item['show_type'] = 2;
            // } else {
            //     $item['show_type'] = 3;
            // }
            if (!empty($shareProductId) && $shareProductId == $product->id) {
                $shareProduct = $item;
            } else {
                $productList[] = $item;
            }
        }

        if (!empty($shareProduct)) {
            array_unshift($productList, $shareProduct);
        }
        return $productList;
    }

    public function getDetailData($user,$nickName = '', $rebatePercentage = 0)
    {
        $customerWeChatId = env('CUSTOMER_WECHAT_ID', '');

        $productInfo = [
            'product_token'   => Codec::encodeId($this->id),
            'title'           => $this->title,
            'type'            => $this->type,
            'price'           => round($this->price, 2),
            'original_price'  => ($this->price != $this->original_price) ? round($this->original_price, 2) : 0,
            //'rebate_amount'   => round($rebatePercentage * $this->price / 100, 2),
            'images'          => Oss::formatImage($this->images, Oss::STYLE_NAME_W800H800),
            //'images'          => Oss::formatImage($this->images, ''),
            'detail_images'   => Oss::formatImage($this->detail_images, Oss::STYLE_NAME_W800),
            'tags'            => $this->tags,
            'sold_num'        => $this->sold_num,
            'advance_tip'     => $this->getAdvanceTip(),
            'customer_tip'    => "添加客服微信号：{$customerWeChatId} 咨询",
            'customer_wechat' => $customerWeChatId,
            'is_sell_out'     => $this->isSellOut() ? 1 : 0,
            'share_title'     => $this->getShareTitle($nickName),
            'share_image'     => Oss::addImageStyle(Oss::STYLE_NAME_W500H400, $this->share_image),
        ];
        $productInfo['show_share'] = 1;

        if($user instanceof User && $user->isDistributorUser()){
            //$productInfo['rebate_amount'] = $this->first_rebate_amount;
            $productInfo['rebate_amount'] = $user->getRebateAmount(1,$this);
            $productInfo['show_type'] = $productInfo['rebate_amount'] > 0 ? 2 : 3;
        }else{
            $productInfo['rebate_amount'] = 0;
            $productInfo['show_type'] = 3;
        }
        //拼团产品 展示拼团
        if($this->isGroupType()){
            $productInfo['show_share'] = 0;
            
        }

        // 展示类型 1:预售 2:分享 3:正常用户
        // if ($this->isAdvanceSale()) {
        //     $productInfo['show_type'] = 1;
        // } else if ($rebatePercentage > 0) {
        //     $productInfo['show_type'] = 2;
        // } else {
        //     $productInfo['show_type'] = 3;
        // }
        return $productInfo;
    }

    /**
     * 获取确认订单数据
     * @return array
     */
    public function getConfirmOrderData()
    {
        $data = [
            'product_token' => Codec::encodeId($this->id),
            'title'         => $this->title,
            'main_img'      => $this->getMainImage(),
            'price'         => round($this->price, 2),
            'buy_min_num'   => $this->buy_min_num,
            'buy_max_num'   => $this->buy_max_num,
            'buy_step'      => $this->buy_step,
        ];
        return $data;
    }

       // 添加销量
       public function addSoldNum($num)
       {
           $this->increment('sold_num', $num);
           $this->decrement('stock', $num);
       }


       public function isGroupType(){
           return self::TYPE_GROUP == $this->type;
       }

       public function getGroupPrice($groupId){
           if($this->isGroupType() && !empty($groupId)){
                $group = Group::find($groupId);
                if($group instanceof Group && $group->product_id == $this->id){
                    return  round($group->price, 2);
                }
           }
           return round($this->price, 2);
       }
}
