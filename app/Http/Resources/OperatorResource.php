<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OperatorResource extends JsonResource
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
            'id'           => (int)$this->id,
            'user_name'    => (string)$this->user_name,
            'real_name'    => (string)$this->real_name,
            'mobile'       => (string)$this->mobile,
            'email'        => (string)$this->email,
            'status'       => (int)$this->status,
            'creator_name' => (string)$this->creator_name,
            'creator_id'   => (string)$this->creator_id,
            'created_at'   => (string)$this->created_at->format('Y-m-d H:i'),
            //'roles'        => RoleResource::collection($this->whenLoaded('roles')),
        );
    }
}
