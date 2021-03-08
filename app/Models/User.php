<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = "users";

    //是否分享员
    const  DISTRIBUTOR_YES = 1;
    const  DISTRIBUTOR_NO  = 0;

    // 分销员等级
    const DISTRIBUTOR_LEVEL_ZERO   = 0;
    const DISTRIBUTOR_LEVEL_FIRST  = 1;
    const DISTRIBUTOR_LEVEL_SECOND = 2;
    const DISTRIBUTOR_LEVEL_THIRD  = 3;

    const DISTRIBUTOR_LEVEL_FIRST_BUY_NUM  = 1;
    const DISTRIBUTOR_LEVEL_SECOND_BUY_NUM = 9;

    public static $distributorRebatePercentage = [
        self::DISTRIBUTOR_LEVEL_ZERO   => 0,
        self::DISTRIBUTOR_LEVEL_FIRST  => 15,
        self::DISTRIBUTOR_LEVEL_SECOND => 20,
    ];

    public static $distributorLevelDesc = [
        self::DISTRIBUTOR_LEVEL_ZERO   => '非分销员',
        self::DISTRIBUTOR_LEVEL_FIRST  => '铜牌分销员',
        self::DISTRIBUTOR_LEVEL_SECOND => '银牌分销员',
        self::DISTRIBUTOR_LEVEL_THIRD  => '金牌分销员',
    ];
    public static $distributorLevelOptions = [
        ['value' => self::DISTRIBUTOR_LEVEL_ZERO, 'label' => '全部用户'],
        ['value' => self::DISTRIBUTOR_LEVEL_FIRST, 'label' => '铜牌分销员'],
        ['value' => self::DISTRIBUTOR_LEVEL_SECOND, 'label' => '银牌分销员'],
        ['value' => self::DISTRIBUTOR_LEVEL_THIRD, 'label' => '金牌分销员'],
    ];

    // 推广上上级返利金额
    const SUPERIOR_DISTRIBUTOR_PERCENTAGE = 5;

    // 是否是预售用户 0:不是 1:是
    const ADVANCE_SALE_NO  = 0;
    const ADVANCE_SALE_YES = 1;

    // 分销员预售等级 0: 无 1:银牌会员 2:金牌会员 3:钻石会员 4:皇冠会员
    const ADVANCE_SALE_LEVEL_ZERO   = 0;
    const ADVANCE_SALE_LEVEL_FIRST  = 1;
    const ADVANCE_SALE_LEVEL_SECOND = 2;
    const ADVANCE_SALE_LEVEL_THREE  = 3;
    const ADVANCE_SALE_LEVEL_FOURTH = 4;
    public static $advanceSaleLevelDesc = [
        self::ADVANCE_SALE_LEVEL_ZERO   => '',
        self::ADVANCE_SALE_LEVEL_FIRST  => '银牌会员',
        self::ADVANCE_SALE_LEVEL_SECOND => '金牌会员',
        self::ADVANCE_SALE_LEVEL_THREE  => '钻石会员',
        self::ADVANCE_SALE_LEVEL_FOURTH => '皇冠会员',
    ];
    public static $advanceSaleLevelOptions = [
        ['value' => '', 'label' => '全部'],
        ['value' => self::ADVANCE_SALE_LEVEL_FIRST, 'label' => '银牌会员'],
        ['value' => self::ADVANCE_SALE_LEVEL_SECOND, 'label' => '金牌会员'],
        ['value' => self::ADVANCE_SALE_LEVEL_THREE, 'label' => '钻石会员'],
        ['value' => self::ADVANCE_SALE_LEVEL_FOURTH, 'label' => '皇冠会员'],
    ];
    public static $advanceSaleGradeNum = [
        self::ADVANCE_SALE_LEVEL_FOURTH => 1000,
        self::ADVANCE_SALE_LEVEL_THREE  => 500,
        self::ADVANCE_SALE_LEVEL_SECOND => 100,
        self::ADVANCE_SALE_LEVEL_FIRST  => 10,
    ];
    public static $advanceSaleRebatePercentage = [
        self::ADVANCE_SALE_LEVEL_ZERO   => 0,
        self::ADVANCE_SALE_LEVEL_FIRST  => 0,
        self::ADVANCE_SALE_LEVEL_SECOND => 5,
        self::ADVANCE_SALE_LEVEL_THREE  => 10,
        self::ADVANCE_SALE_LEVEL_FOURTH => 15
    ];
    const USER_LEVEL_1 = 1;
    const USER_LEVEL_2 = 2;
    const USER_LEVEL_3 = 3;
    const USER_LEVEL_4 = 4;
    const USER_LEVEL_5 = 5;
    public static $userLevelDesc = [
        self::USER_LEVEL_1 => '会员',
        self::USER_LEVEL_2 => '银牌会员',
        self::USER_LEVEL_3 => '金牌会员',
        self::USER_LEVEL_4 => '钻石会员',
        self::USER_LEVEL_5 => '皇冠会员',
    ];
    // 预售用户推广返利
    const ADVANCE_SALE_DISTRIBUTOR_PERCENTAGE = 5;
    // 微信客服微信号
    const CUSTOMER_WECHAT_ID = 'yiqitonggan';

    // 创建新用户
    public static function createByWxUser(WxUser $wxUser)
    {
        $user             = new self();
        $user->union_id   = $wxUser->union_id;
        $user->nick_name  = $wxUser->nick_name??'';
        $user->head_image = $wxUser->avatar??'';
        $user->save();

        $relationship = UserRelationship::where('wx_user_id', $wxUser->id)->where('user_id', 0)->first();
        if ($relationship instanceof UserRelationship) {
            $relationship->bindUserId($user->id);
        }

        $coupons = Coupon::where('wx_user_id',$wxUser->id)->where('user_id',0)->get();
        foreach($coupons as $coupon){
            $coupon->bindUserId($user->id);
        }
        return $user;
    }

    public function updateByWxUser(WxUser $wxUser)
    {
        $this->nick_name  = $wxUser->nick_name;
        $this->head_image = $wxUser->avatar;
        return $this->save();
    }

    // 是否是分享员
    public function isDistributorUser()
    {
        return self::DISTRIBUTOR_YES == $this->is_distributor;
    }

    // 是否是预售用户
    public function isAdvanceSaleUser()
    {
        return self::ADVANCE_SALE_YES == $this->is_advance_sale;
    }

    public function upgradeDistributor()
    {
        //升级暂时不做
        return ;
        $childUserIds   = UserRelationship::where('parent_id', $this->user_id)
            ->where('user_id', '>', 0)->pluck('user_id')->toArray();
        $childUserIds[] = $this->id;

        $orderCnt = Order::whereIn('user_id', $childUserIds)
            ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_SHIPPED, Order::STATUS_RECEIVED])
            ->where('product_type', Product::TYPE_NORMAL)
            ->sum('buy_num');

        if ($orderCnt >= self::DISTRIBUTOR_LEVEL_SECOND_BUY_NUM) {
            $this->is_distributor = self::DISTRIBUTOR_YES;
            $this->distributor_level = self::DISTRIBUTOR_LEVEL_SECOND;
        } else if ($orderCnt >= self::DISTRIBUTOR_LEVEL_FIRST) {
            $this->is_distributor = self::DISTRIBUTOR_YES;
            $this->distributor_level = self::DISTRIBUTOR_LEVEL_FIRST;
        }
        return $this->save();
    }
    //暂时去掉
    public function updateDistributorLevel($distributorLevel)
    {
        $this->distributor_level = $distributorLevel;
        return $this->save();
    }
    public function setDistributorLevel($distributorLevel){
        if(empty($distributorLevel)){
            $this->is_distributor = self::DISTRIBUTOR_NO;
        }else{
            $this->is_distributor = self::DISTRIBUTOR_YES;
        }
        $this->distributor_level = $distributorLevel;
        return $this->save();
    }

    public function becomeAdvanceSale($advanceSaleLevel)
    {
        $this->is_distributor     = self::DISTRIBUTOR_YES;
        $this->distributor_level  = self::DISTRIBUTOR_LEVEL_SECOND;
        $this->is_advance_sale    = self::ADVANCE_SALE_YES;
        $this->advance_sale_level = $advanceSaleLevel;
        $this->save();

        $income = DistributorIncome::where('user_id', $this->id)->first();
        if (!$income instanceof DistributorIncome) {
            DistributorIncome::add($this->id);
        }
        return true;
    }
    public function updateAdvanceSaleLevel($advanceSaleLevel)
    {
        $this->advance_sale_level = $advanceSaleLevel;
        $this->save();
        return true;
    }

    public function isFirstDistributor()
    {
        return self::DISTRIBUTOR_LEVEL_FIRST == $this->distributor_level;
    }

    public function isSecondDistributor()
    {
        return self::DISTRIBUTOR_LEVEL_SECOND == $this->distributor_level;
    }

    public function isThirdDistributor()
    {
        return self::DISTRIBUTOR_LEVEL_THIRD == $this->distributor_level;
    }
    public function getSpreadPercentage()
    {
        return self::$distributorRebatePercentage[$this->distributor_level] ?? 0;
    }

    public function getUserLevel()
    {
        $level = 0;
        if ($this->is_advance_sale) {
            $level = $this->advance_sale_level + 1;
        } else if ($this->is_distributor) {
            $level = $this->distributor_level;
        }
        return $level;
    }

    public function replenishIdCardAuth($params)
    {
        if (!empty($params['real_name'])) {
            $this->real_name = $params['real_name'];
        }
        if (!empty($params['mobile'])) {
            $this->mobile           = $params['mobile'];
            $this->mobile_bind_time = now()->toDateTimeString();
        }
        if (!empty($params['id_card'])) {
            $this->id_card = $params['id_card'];
        }
        return $this->save();
    }


    public function getRebateAmount($level,$product){
        //一级
        if($level == 1){
            if($this->isFirstDistributor()){
                return $product->first_rebate_amount;
            }elseif($this->isSecondDistributor()){
                return $product->s_first_rebate_amount;
            }elseif($this->isThirdDistributor()){
                return $product->t_first_rebate_amount;                
            }else{
                return 0;
            }
        }
        //二级
        elseif($level == 2){
            if($this->isFirstDistributor()){
                return $product->second_rebate_amount;
            }elseif($this->isSecondDistributor()){
                return $product->s_second_rebate_amount;
            }elseif($this->isThirdDistributor()){
                return $product->t_second_rebate_amount;                
            }else{
                return 0;
            }
        }
        return 0;
    }
}
