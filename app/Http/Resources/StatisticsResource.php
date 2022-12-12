<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'order_date'=>$this->order_date,
            'sales'=>$this->sales,
            'profit'=>$this->profit,
            'quantity'=>$this->quantity,
            'total_order'=>$this->total_order,
        ];
    }
}