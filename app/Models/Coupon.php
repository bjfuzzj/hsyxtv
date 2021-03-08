<?php
/*
 * @Author: bjfuzzj
 * @Date: 2020-09-17 14:02:54
 * @LastEditTime: 2021-01-23 16:17:50
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /sg/songguo/app/Models/Coupon.php
 */

namespace App\Models;

use App\Http\Resources\CouponResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class Coupon
 * @package App\Models
 *
 * @property int id ID
 * @property int user_id 用户ID
 * @property int template_id 优惠券模板ID
 * @property int status 状态
 * @property string name 优惠券名称
 * @property int type 优惠券类型 1:直减 2:满减 3:折减
 * @property float cut_value 减多少；直减和满减为金额，折减为折扣
 * @property float cost 消费多少金额可以使用，用于满减类型
 * @property string valid_start_time 有效起始时间
 * @property string valid_end_time 有效截止时间
 * @property string invalid_cause 失效原因
 * @property string use_time 使用时间
 * @property int order_id 订单ID
 * @property string created_at 创建时间
 * @property string updated_at 更新时间
 */
class Coupon extends Model
{
    protected $table = 'coupons';

    protected $appends = ['status_desc','type_desc'];

    //状态 1:未使用 2:已使用 3:已失效 4:转赠中 5:已转赠
    const STATUS_NOT_USE = 1;
    const STATUS_USED    = 2;
    const STATUS_INVALID = 3;
    const STATUS_TRANING = 4;
    const STATUS_TRANED  = 5;
    public static $statusDesc = [
        self::STATUS_NOT_USE => '未使用',
        self::STATUS_USED    => '已使用',
        self::STATUS_INVALID => '已失效',
        self::STATUS_TRANING => '转赠中',
        self::STATUS_TRANED => '已转赠',
    ];

    public static $statusOptions = [
        ['value' => '', 'label' => '全部'],
        ['value' => self::STATUS_NOT_USE, 'label' => '未使用'],
        ['value' => self::STATUS_USED, 'label' => '已使用'],
        ['value' => self::STATUS_INVALID, 'label' => '已失效'],
        ['value' => self::STATUS_TRANING, 'label' => '转赠中'],
        ['value' => self::STATUS_TRANED, 'label' => '已转赠'],
    ];

    public function getTypeDescAttribute()
    {
        return CouponTemplate::$typeDesc[$this->type] ?? '';
    }

    public function getStatusDescAttribute()
    {
        return self::$statusDesc[$this->status] ?? '';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function couponTemplate()
    {
        return $this->belongsTo(CouponTemplate::class, 'template_id');
    }

    public function isNotUse()
    {
        return self::STATUS_NOT_USE == $this->status;
    }

    public function setTransing(){
        if($this->status != self::STATUS_TRANING){
            $this->status = self::STATUS_TRANING;
        }
        return $this->save();
    }

    public function getDiscountUnit()
    {
        if (CouponTemplate::TYPE_DIRECT_CUT == $this->type || CouponTemplate::TYPE_FULL_CUT == $this->type) {
            return '元';
        } else if (CouponTemplate::TYPE_DISCOUNT_CUT == $this->type) {
            return '折';
        }
        return '';
    }

    public function getDiscountDesc()
    {
        if (CouponTemplate::TYPE_DIRECT_CUT == $this->type) {
            return round($this->cut_value, 2) . '元无门槛券   ';
        } else if (CouponTemplate::TYPE_FULL_CUT == $this->type) {
            return '满' . round($this->cost, 2) . '元减' . round($this->cut_value, 2) . '元';
        } else if (CouponTemplate::TYPE_DISCOUNT_CUT == $this->type) {
            return '满' . round($this->cost, 2) . '元打' . round($this->cut_value, 1) . '折';
        }
        return '';
    }

    public function getAmountCoupon($amountTotal)
    {
        //直减
        if (CouponTemplate::TYPE_DIRECT_CUT == $this->type && ($amountTotal-$this->cut_value)>=0 ) {
            return round($this->cut_value, 2);
        } 
        //满减
        else if (CouponTemplate::TYPE_FULL_CUT == $this->type) {
            if($amountTotal >= $this->cost && ($amountTotal-$this->cut_value)>=0){
                return round($this->cut_value, 2);
            }
        } 
        //打折
        else if (CouponTemplate::TYPE_DISCOUNT_CUT == $this->type) {
            return round(($amountTotal * (100-$this->cut_value))/100, 2);
        }
        return '0';
    }

    public static function getUserCouponList($userId, $params)
    {
        $page = !empty($params['page']) && $params['page'] > 0 ? $params['page'] : 1;
        $size = !empty($params['size']) && $params['size'] > 0 ? $params['size'] : 20;

        $query = self::where('user_id', $userId);
        if (!empty($params['status'])) {
            $query->where('status', $params['status']);
        }
        $queryRes = $query->paginate($size, ['*'], 'page', $page);

        return [
            'per_page'     => $queryRes->perPage(),
            'current_page' => $queryRes->currentPage(),
            'last_page'    => $queryRes->lastPage(),
            'total'        => $queryRes->total(),
            'has_more'     => $queryRes->hasMorePages() ? 1 : 0,
            'list'         => CouponResource::collection($queryRes),
        ];
    }

    public static function getUsableCouponByProduct($userId, $productId)
    {
        $templateIds = CouponTemplate::getTemplateIdByProduct($productId);

        $queryRes = self::where('user_id', $userId)->where('status', self::STATUS_NOT_USE)
            ->whereIn('template_id', $templateIds)->groupBy('template_id')->get();
        return CouponResource::collection($queryRes);
    }


    public function setUse($orderId)
    {
        $this->status = self::STATUS_USED;
        $this->order_id = $orderId;
        return $this->save();
        
    }


    public function trans($newWxUser){

        $newUserId = $newWxUser->user_id;
     
        if($newUserId != $this->user_id) {
            DB::beginTransaction();
            try {
                    $Newcoupon                   = new Coupon();
                    $Newcoupon->user_id          = $newUserId;
                    $Newcoupon->wx_user_id       = $newWxUser->id;
                    $Newcoupon->template_id      = $this->template_id;
                    $Newcoupon->name             = $this->name;
                    $Newcoupon->type             = $this->type;
                    $Newcoupon->cut_value        = $this->cut_value;
                    $Newcoupon->cost             = $this->cost;
                    $Newcoupon->valid_start_time = $this->valid_start_time;
                    $Newcoupon->valid_end_time   = $this->valid_end_time;
                    $Newcoupon->status           = Coupon::STATUS_NOT_USE;
                    $Newcoupon->save();
                    $this->status = Coupon::STATUS_TRANED;
                    $this->save();
                DB::commit();
                return ['isSuccess' => 1, 'msg' => '领取成功' , 'data'=>$Newcoupon->id];
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error(__CLASS__ . ':' . __FUNCTION__ . " FromUserId:{$this->user_id} couponId:{$this->id} toUserId:{$newUserId}"
                    . 'errMsg:' . $e->getMessage());
                return ['isSuccess' => 0, 'msg' => '领取优惠券失败-7' ,'data'=> ''];
            }
        }
        return ['isSuccess' => 0, 'msg' => '领取优惠券失败-8','data'=>''];
    }

    public function bindUserId($userId)
     {
         $this->user_id = $userId;
         return $this->save();
     }
}
