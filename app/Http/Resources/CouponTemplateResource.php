<?php
/*
 * @Author: bjfuzzj
 * @Date: 2020-10-24 18:55:35
 * @LastEditTime: 2020-10-30 23:36:11
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /sg/songguo/app/Http/Resources/CouponTemplateResource.php
 */

namespace App\Http\Resources;

use App\Models\Coupon;
use App\Models\CouponTemplate;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return array(
            'id'                 => $this->id,
            'name'               => $this->name,
            'type'               => $this->type,
            'type_desc'          => CouponTemplate::$typeDesc[$this->type] ?? '',
            'promote_type'       => $this->promote_type,
            'promote_type_desc'  => CouponTemplate::$promoteTypeDesc[$this->promote_type] ?? '',
            'status'             => $this->status,
            'status_desc'        => CouponTemplate::$statusDesc[$this->status] ?? '',
            'cut_value'          => $this->cut_value,
            'cost'               => $this->cost,
            'total'              => $this->total,
            'used_cnt'           => $this->used_cnt,
            'person_limit_num'   => $this->person_limit_num,
            'discount_desc'      => $this->getDiscountDesc(),
            'valid_type'         => $this->valid_type,
            'valid_type_desc'    => CouponTemplate::$validTypeDesc[$this->valid_type] ?? '',
            'valid_day'          => $this->valid_day,
            'valid_start_time'   => $this->valid_start_time,
            'valid_end_time'     => $this->valid_end_time,
            'receive_start_time' => $this->receive_start_time,
            'receive_end_time'   => $this->receive_end_time,
            'creator_name'       => $this->creator_name,
            'creator_id'         => $this->creator_id,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
            'deleted_at'         => $this->deleted_at,
            'src'                => $this->src,
        );
    }
}
