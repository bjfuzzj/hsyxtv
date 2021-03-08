<?php
/*
 * @Author: your name
 * @Date: 2020-12-22 21:38:29
 * @LastEditTime: 2021-01-01 21:56:07
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Http/Resources/CouponResource.php
 */

namespace App\Http\Resources;

use App\Models\Coupon;
use App\Models\CouponTemplate;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id'                  => $this->id,
            'user_id'             => $this->user_id,
            'type'                => $this->type,
            'type_desc'           => CouponTemplate::$typeDesc[$this->type] ?? '',
            'status'              => $this->status,
            'status_desc'         => Coupon::$statusDesc[$this->status] ?? '',
            'name'                => $this->name,
            'cut_value'           => $this->cut_value,
            'cost'                => $this->cost,
            'discount_unit'       => $this->getDiscountUnit(),
            'discount_desc'       => $this->getDiscountDesc(),
            'valid_start_time'    => !empty($this->valid_start_time) ? Carbon::parse($this->valid_start_time)->getTimestamp() : 0,
            'valid_start_time_zh' => !empty($this->valid_start_time) ? Carbon::parse($this->valid_start_time)->format('Y-m-d') : '',
            'valid_end_time'      => !empty($this->valid_end_time) ? Carbon::parse($this->valid_end_time)->getTimestamp() : 0,
            'valid_end_time_zh'   => !empty($this->valid_end_time) ? Carbon::parse($this->valid_end_time)->format('Y-m-d') : '',
            'is_used'             => !empty($this->use_time) ? 1 : 0,
            'use_time_zh'         => !empty($this->use_time) ? Carbon::parse($this->use_time)->format('Y年m月m日 H:i') : '',
            'user_info'           => UserBaseResource::make($this->whenLoaded('user')),
        ];
        return $data;
    }
}
