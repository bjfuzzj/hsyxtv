<?php
/*
 * @Author: your name
 * @Date: 2021-01-28 00:14:14
 * @LastEditTime: 2021-02-02 18:05:10
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Models/Activity.php
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Activity extends Model
{
    protected $table = 'activity';

    protected $appends = ['status_desc'];
    //产品状态 0:失效 1:有效
    const  STATUS_INVALID      = 0;
    const  STATUS_VALID        = 1;
    
    public static $statusDesc = [
        self::STATUS_INVALID    => '失效',
        self::STATUS_VALID      => '有效',
    ];

    public static $statusOptions = [
        ['value' => '', 'label' => '全部'],
        ['value' => self::STATUS_INVALID, 'label' => '失效'],
        ['value' => self::STATUS_VALID, 'label' => '有效'],
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getStatusDescAttribute()
    {
        return self::$statusDesc[$this->status] ?? '';
    }

    public function isInValid(){
        return self::STATUS_INVALID == $this->status;
    }

        
    public static function add($data)
    {
        DB::beginTransaction();
        try {
            if (!empty($data['id'])) {
                $activity = self::find($data['id']);
                if ($activity instanceof self) {
                    $activity->setData($data);
                    $activity->status = $data['status']??'0';
                    $activity->save();
                }
            } else {
                  //商品管理
                if(empty($data['product_id'])){
                    return ['code'=>'fail','msg'=>'缺少商品ID参数'];
                }
                $productId = $data['product_id'];
                $product = Product::find($productId);
                if(!$product instanceof Product){
                    return ['code'=>'fail','msg'=>'商品不存在'];
                }
                $activity = Activity::where('product_id',$productId)->where('status',self::STATUS_VALID)->first();
                if($activity instanceof Activity){
                    return ['code'=>'fail','msg'=>'此商品已有上线的拼团活动！'];
                }
                $activity = new self();
                $activity->setData($data);
                $activity->product_id = $productId;
                $product->setGroupType();
                $activity->save();
            }
            DB::commit();
            return ['code'=>'succ','msg'=>''];
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error(__CLASS__ . '_' . __FUNCTION__ . ' 添加失败:'.$e->getMessage(), \func_get_args());
            return ['code'=>'fail','msg'=>$e->getMessage()];
        }
    }

    private function setData($data)
    {
        $this->name           = $data['name'] ?? '';
        $this->full_amount    = $data['full_amount'] ?? 0;
        $this->original_price = $data['original_price'] ?? 0;
        $this->price          = $data['price'] ?? 1;
        $this->valid_day      = $data['valid_day'] ?? 0;
        $this->buy_start_time = $data['buy_start_time'] ?? '';
        $this->buy_end_time   = $data['buy_end_time'] ?? '';
    }
    
}
