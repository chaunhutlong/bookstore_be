<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AddressResource;
use App\Models\Address;

class ShippingResource extends JsonResource
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
            'id' => $this->id,
            'tracking_num' => $this->tracking_num,
            'order_id' => $this->order_id,
            'address_id' => $this->address_id,
            'value' => $this->value,
            'address_detail' => new AddressResource(Address::find($this->address_id)),
            'note' => $this->description,
        ];
    }
}