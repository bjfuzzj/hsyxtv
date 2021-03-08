<?php
/*
 * @Author: your name
 * @Date: 2021-01-27 22:18:48
 * @LastEditTime: 2021-02-02 18:37:44
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Models/Group.php
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Group extends Model
{
    protected $table = 'group_activity';

    protected $appends = ['status_desc'];


    //产品状态 0:待成团 1:拼团中 2:拼团成功 3:拼团失败
    const  STATUS_UNPAY       = 0;
    const  STATUS_ING         = 1;
    const  STATUS_SUCC        = 2;
    const  STATUS_FAIL        = 3;
    public static $statusDesc = [
        self:: STATUS_UNPAY    => '待成团',
        self:: STATUS_ING  => '拼团中',
        self:: STATUS_SUCC => '拼团成功',
        self:: STATUS_FAIL => '拼团失败',
    ];
    public static $statusOptions = [
        ['value' => '', 'label' => '全部'],
        ['value' => self::STATUS_UNPAY, 'label' => '待成团'],
        ['value' => self::STATUS_ING,   'label' => '拼团中'],
        ['value' => self::STATUS_SUCC,  'label'  => '拼团成功'],
        ['value' => self::STATUS_FAIL,  'label'  => '拼团失败'],
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function founder()
    {
        return $this->belongsTo(User::class, 'founder_id');
    }

    public function getStatusDescAttribute()
    {
        return self::$statusDesc[$this->status] ?? '';
    }

    public function isFail(){
        return self::STATUS_FAIL == $this->status;
    }

    public function isSucc(){
        return self::STATUS_SUCC == $this->status;
    }

    public static function create($user, $product)
    {
        $activity = Activity::where('product_id',$product->id)->where('status',Activity::STATUS_VALID)->first();
        if(!$activity instanceof Activity) return false;
        $group              = new self();
        $group->activity_id = $activity->id;
        $group->founder_id  = $user->id;
        $group->status      = self::STATUS_ING;
        $group->product_id  = $product->id;
        $group->full_amount = 1;
        $group->price       = $activity->price;
        $group->start_time  = Carbon::now();
        $group->end_time    = Carbon::now()->addDays($activity->valid_day);
        $group->save();
        return $group->id;
    }

    public function addMemberCount(){
        $activity = Activity::find($this->activity_id);
        if($activity instanceof Activity){
            $this->full_amount = $this->full_amount + 1;
            if($this->full_amount == $activity->full_amount){
                $this->setSucc();
            }
        }
        return $this->save();        
    }

    public function setSucc(){
        $this->status = self::STATUS_SUCC;
        $this->save();
    }
    
}
