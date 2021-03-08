<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DistributorIncomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'income_total'  => sprintf('%.2f', $this->income_total),
            'income_used'   => sprintf('%.2f', $this->income_used),
            'income_usable' => sprintf('%.2f', $this->income_usable),
            'income_freeze' => sprintf('%.2f', $this->income_freeze),
        ];
    }
}
