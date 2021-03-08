<?php
/*
 * @Author: your name
 * @Date: 2021-01-27 22:18:54
 * @LastEditTime: 2021-02-02 22:03:59
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Models/GroupTeam.php
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupTeam extends Model
{
    protected $table = 'group_team';
    protected $appends = ['role_desc'];
    
    const ROLE_LEADER = 1;
    const ROLE_MEMBER = 0;
    public static $roleDesc = [
        self:: ROLE_MEMBER  => '成员',
        self:: ROLE_LEADER  => '团长',
    ];
    
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function getRoleDescAttribute()
    {
        return self::$roleDesc[$this->role] ?? '';
    }
    
   

    public static function add(Order $order, $role)
    {
        $activity = Activity::where('product_id',$order->product_id)->where('status',Activity::STATUS_VALID)->first();
        if(!$activity instanceof Activity) return false;
        $member              = new self();
        $member->activity_id = $activity->id;
        $member->group_id    = $order->group_id;
        $member->order_id    = $order->id;
        $member->product_id  = $order->product_id;
        $member->user_id     = $order->user_id;
        $member->role        = $role;
        $member->save();

        if($role == self::ROLE_MEMBER){
            $group = Group::find($order->group_id);
            if($group instanceof Group){
                $group->addMemberCount();
            }
        }
        return $member;
    }
}
