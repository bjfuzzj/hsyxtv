<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\CouponResource;

/**
 * 优惠券模板
 * Class CouponTemplate
 * @package App\Models
 *
 * @property int id ID
 * @property string name 优惠券名称
 * @property int type 优惠券类型
 * @property int promote_type 推广类型
 * @property int status 状态
 * @property float cut_value 减多少；直减和满减为金额，折减为折扣
 * @property float cost 消费多少金额可以使用，用于满减类型
 * @property int total 优惠券数量
 * @property int used_cnt 已领取优惠券数量
 * @property int person_limit_num 每个人领取多少张
 * @property int valid_type 有效期类型
 * @property int valid_day 优惠券有效期天数
 * @property string valid_start_time 优惠券有效开始时间
 * @property string valid_end_time 优惠券有效结束时间
 * @property string receive_start_time 领券开始时间
 * @property string receive_end_time 领券结束时间
 * @property string creator_name 活动创建者名称
 * @property int creator_id 活动创建者ID
 * @property string created_at 创建时间
 * @property string updated_at 更新时间
 * @property string deleted_at 删除时间
 */
class CouponTemplate extends Model
{
    protected $table = 'coupon_templates';

    protected $appends = ['status_desc', 'type_desc', 'promote_type_desc', 'valid_type_desc'];

    // 状态
    const STATUS_DRAFT   = 1;
    const STATUS_ONLINE  = 2;
    const STATUS_OFFLINE = 3;
    public static $statusDesc = [
        self::STATUS_DRAFT   => '待发布',
        self::STATUS_ONLINE  => '已发布',
        self::STATUS_OFFLINE => '下线',
    ];
    public static $statusOptions = [
        ['value' => '', 'label' => '全部'],
        ['value' => self::STATUS_DRAFT, 'label' => '待发布'],
        ['value' => self::STATUS_ONLINE, 'label' => '已发布'],
        ['value' => self::STATUS_OFFLINE, 'label' => '下线'],
    ];

    // 优惠券类型 1:直减 2:满减 3:折减
    const TYPE_DIRECT_CUT   = 1;
    const TYPE_FULL_CUT     = 2;
    const TYPE_DISCOUNT_CUT = 3;
    public static $typeDesc = [
        self::TYPE_DIRECT_CUT   => '直减',
        self::TYPE_FULL_CUT     => '满减',
        self::TYPE_DISCOUNT_CUT => '折减',
    ];
    public static $typeOptions = [
        ['value' => '', 'label' => '全部'],
        ['value' => self::TYPE_DIRECT_CUT, 'label' => '直减'],
        ['value' => self::TYPE_FULL_CUT, 'label' => '满减'],
        ['value' => self::TYPE_DISCOUNT_CUT, 'label' => '折减'],
    ];

    // 推广类型 1:平台 2:指定商品
    const PROMOTE_TYPE_PLATFORM = 1;
    const PROMOTE_TYPE_PRODUCT  = 2;

    public static $promoteTypeDesc = [
        self::PROMOTE_TYPE_PLATFORM => '平台券',
        self::PROMOTE_TYPE_PRODUCT  => '商品券',
    ];
    public static $promoteTypeOptions = [
        ['value' => '', 'label' => '全部'],
        ['value' => self::PROMOTE_TYPE_PLATFORM, 'label' => '全平台'],
        ['value' => self::PROMOTE_TYPE_PRODUCT, 'label' => '指定商品'],
    ];


    // 有效期类型 1：限定日期有效 2: 领取n天有效
    const VALID_TYPE_RANGE = 1;
    const VALID_TYPE_DAY   = 2;

    public static $validTypeDesc = [
        self::VALID_TYPE_RANGE => '限定日期有效',
        self::VALID_TYPE_DAY   => '领取n天有效',
    ];

    public static $validTypeOptions = [
        ['value' => '', 'label' => '全部'],
        ['value' => self::VALID_TYPE_RANGE, 'label' => '限定日期有效'],
        ['value' => self::VALID_TYPE_DAY, 'label' => '领取n天有效'],
    ];


    public function getStatusDescAttribute()
    {
        return self::$statusDesc[$this->status] ?? '';
    }

    public function getTypeDescAttribute()
    {
        return self::$typeDesc[$this->type] ?? '';
    }

    public function getPromoteTypeDescAttribute()
    {
        return self::$promoteTypeDesc[$this->promote_type] ?? '';
    }

    public function getValidTypeDescAttribute()
    {
        return self::$validTypeDesc[$this->valid_type] ?? '';
    }

    public static function addTemplate($data)
    {
        $self = new self();
        $self->setData($data);
        $self->status = self::STATUS_DRAFT;
        $self->save();
        return $self;
    }

    public function updateTemplate($data)
    {
        $this->setData($data);
        return $this->save();
    }

    private function setData($data)
    {
        $this->name               = $data['name'] ?? '';
        $this->type               = $data['type'] ?? self::TYPE_FULL_CUT;
        $this->promote_type       = $data['promote_type'] ?? self::PROMOTE_TYPE_PLATFORM;
        $this->cut_value          = $data['cut_value'] ?? 0;
        $this->cost               = $data['cost'] ?? 0;
        $this->total              = $data['total'] ?? 0;
        $this->person_limit_num   = $data['person_limit_num'] ?? 1;
        $this->valid_type         = $data['valid_type'] ?? 1;
        $this->valid_day          = $data['valid_day'] ?? 0;
        $this->valid_start_time   = $data['valid_start_time'] ?? '';
        $this->valid_end_time     = $data['valid_end_time'] ?? '';
        $this->receive_start_time = $data['receive_start_time'] ?? '';
        $this->receive_end_time   = $data['valid_end_time'] ?? '';
        $this->creator_name       = $data['creator_name'] ?? '';
        $this->creator_id         = $data['creator_id'] ?? 0;
        $this->src                = $data['src'] ?? '';
        $this->is_show            = $data['is_show'] ?? 0;
    }

    public function setOnline()
    {
        $this->status = self::STATUS_ONLINE;
        return $this->save();
    }

    public function setOffline()
    {
        $this->status = self::STATUS_OFFLINE;
        return $this->save();
    }

    public function isExpired()
    {
        $now = now()->toDateTimeString();
        return self::VALID_TYPE_RANGE == $this->valid_type && $this->valid_end_time < $now;
    }

    public function receiveCoupon(User $user, $receiveCount = 1)
    {
        $wxUser = WxUser::where('user_id',$user->id)->first();
        if(!$wxUser instanceof WxUser){
            return ['isSuccess' => 0, 'msg' => '领取失败，用户信息出错'];
        }
        if (self::STATUS_ONLINE != $this->status) {
            return ['isSuccess' => 0, 'msg' => '领取失败，优惠券已下线'];
        }
        if ($this->isExpired()) {
            return ['isSuccess' => 0, 'msg' => '领取失败，优惠券已过期'];
        }

        if ($this->person_limit_num > 0) {
            $couponCnt = Coupon::where('user_id', $user->id)->where('template_id', $this->id)
                ->where('status', '<>', Coupon::STATUS_INVALID)->count();
            if (($couponCnt + $receiveCount) > $this->person_limit_num) {
                return ['isSuccess' => 0, 'msg' => "您已经领取了，该优惠券每人最多可领取{$this->person_limit_num}张"];
            }
        }


        if (self::VALID_TYPE_RANGE == $this->valid_type) {
            $validStartTime = $this->valid_start_time;
            $validEndTime   = $this->valid_end_time;
        } else {
            $validStartTime = today()->toDateTimeString();
            $validEndTime   = today()->addDays($this->valid_day)->toDateTimeString();
        }

        DB::beginTransaction();
        try {
            $couponCnt = $receiveCount;
            while ($couponCnt--) {
                $coupon                   = new Coupon();
                $coupon->user_id          = $user->id;
                $coupon->wx_user_id       = $wxUser->id;
                $coupon->template_id      = $this->id;
                $coupon->name             = $this->name;
                $coupon->type             = $this->type;
                $coupon->cut_value        = $this->cut_value;
                $coupon->cost             = $this->cost;
                $coupon->valid_start_time = $validStartTime;
                $coupon->valid_end_time   = $validEndTime;
                $coupon->status           = Coupon::STATUS_NOT_USE;
                $coupon->save();
            }

            $this->increment('used_cnt', $receiveCount);

            DB::commit();
            return ['isSuccess' => 1, 'msg' => '领取成功'];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error(__CLASS__ . ':' . __FUNCTION__ . " userId:{$user->id} templateId:{$this->id} cnt:{$receiveCount}"
                . 'errMsg:' . $e->getMessage());
            return ['isSuccess' => 0, 'msg' => '领取优惠券失败'];
        }
    }

    public function getReceiveResult($userId)
    {
        $receiveCnt = Coupon::where('user_id', $userId)->where('template_id', $this->id)
            ->where('status', '<>', Coupon::STATUS_INVALID)->count();

        $result = ['receive_cnt' => $receiveCnt, 'is_full_received' => 0];
        if ($receiveCnt >= $this->person_limit_num) {
            $result['is_full_received'] = 1;
        }
        return $result;
    }

    public function getDiscountUnit()
    {
        if (self::TYPE_DIRECT_CUT == $this->type || self::TYPE_FULL_CUT == $this->type) {
            return '元';
        } else if (self::TYPE_DISCOUNT_CUT == $this->type) {
            return '折';
        }
        return '';
    }

    public function getDiscountDesc()
    {
        if (self::TYPE_DIRECT_CUT == $this->type) {
            return round($this->cut_value, 2) . '元无门槛券   ';
        } else if (self::TYPE_FULL_CUT == $this->type) {
            return '满' . round($this->cost, 2) . '元减' . round($this->cut_value, 2) . '元';
        } else if (self::TYPE_DISCOUNT_CUT == $this->type) {
            return '满' . round($this->cost, 2) . '元打' . round($this->cut_value, 1) . '折';
        }
        return '';
    }

    public function getValidTimeDesc()
    {
        if (self::VALID_TYPE_DAY == $this->valid_type) {
            return '领券后' . $this->valid_day . '天内有效';
        } else if (self::VALID_TYPE_RANGE == $this->valid_type) {
            return Carbon::parse($this->valid_start_time)->format('Y-m-d') . '至' . Carbon::parse($this->valid_end_time)->format('Y-m-d');
        }
        return '';
    }

    public static function add($data)
    {
        DB::beginTransaction();
        try {
            if (!empty($data['id'])) {
                $couponTemplate = self::find($data['id']);
                if ($couponTemplate instanceof self) {
                    $couponTemplate->setData($data);
                    $couponTemplate->status = $data['status']??'1';
                    $couponTemplate->save();
                }
            } else {
                $couponTemplate = new self();
                $couponTemplate->setData($data);
                $couponTemplate->save();
            }
            if(!empty($data['product_ids'])){
                $productIds = @explode(',',$data['product_ids']);
                if(is_array($productIds) && !empty($productIds)){
                    CouponTemplateProductRef::where('template_id',$couponTemplate->id)->delete();
                    foreach($productIds as $productId){
                        CouponTemplateProductRef::create($productId,$couponTemplate->id);
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error(__CLASS__ . '_' . __FUNCTION__ . ' 添加优惠券失败:'.$e->getMessage(), \func_get_args());
            return false;
        }
    }

    public static function getTemplateIdByProduct($productId)
    {
        $now            = now()->toDateTimeString();
        $productTempIds = CouponTemplateProductRef::where('product_id', $productId)->pluck('template_id')->toArray();

        $templateIds = self::where('status', self::STATUS_ONLINE)
            ->where(function ($query) use ($productTempIds) {
                $query->where('promote_type', self::PROMOTE_TYPE_PLATFORM)->orWhereIn('id', $productTempIds);
            })
            ->where('receive_end_time', '>=', $now)
            ->pluck('id')->toArray();
        return $templateIds;
    }

    public static function getProductUsableCoupon($productId, $currUserId = 0)
    {
        $templateIds = self::getTemplateIdByProduct($productId);
        $receiveCntArr = Coupon::selectRaw('template_id, count(1) as cnt')
            ->where('user_id', $currUserId)
            ->whereIn('template_id', $templateIds)
            ->where('status', '<>', Coupon::STATUS_INVALID)
            ->groupBy('template_id')
            ->pluck('cnt', 'template_id')
            ->toArray();
        $queryRes = self::whereIn('id', $templateIds)->orderByDesc('type')->orderByDesc('id')->get();

        $tempList = [];
        foreach ($queryRes as $item) {
            $receiveCnt = $receiveCntArr[$item->id] ?? 0;

            $tempItem = [
                'id'               => $item->id,
                'name'             => $item->name,
                'promote_type'     => self::$promoteTypeDesc[$item->promote_type] ?? '',
                'cut_value'        => round($item->cut_value, 2),
                'discount_unit'    => $item->getDiscountUnit(),
                'discount_desc'    => $item->getDiscountDesc(),
                'valid_time_desc'  => $item->getValidTimeDesc(),
                'receive_cnt'      => $receiveCnt,
                'is_full_received' => 0,
            ];

            if ($receiveCnt >= $item->person_limit_num) {
                $tempItem['is_full_received'] = 1;
            }
            $tempList[] = $tempItem;
        }
        return $tempList;
    }


    public static function getUserCoupon($currUserId,$params)
    {
        $page = $params['page']>0 ? $params['page'] : 1;
        $size = isset($params['size']) ? $params['size'] : 20;
        $query = Coupon::where('user_id', $currUserId);
        $queryRes = $query->paginate($size, ['*'], 'page', $page);
        $res = CouponResource::collection($queryRes);
        $list = [];
        $list['use'] = [];
        $list['no_use'] = [];

        foreach($res as $singleCoupon){
            if(in_array($singleCoupon['status'],[Coupon::STATUS_NOT_USE,Coupon::STATUS_TRANING])){
                $list['use'][] = $singleCoupon;
            } else {
                $list['no_use'][] = $singleCoupon;
            }
        }
        return [
            'per_page'     => $queryRes->perPage(),
            'current_page' => $queryRes->currentPage(),
            'last_page'    => $queryRes->lastPage(),
            'total'        => $queryRes->total(),
            'has_more'     => $queryRes->hasMorePages() ? 1 : 0,
            'list'         => $list,
        ];
    }

}
