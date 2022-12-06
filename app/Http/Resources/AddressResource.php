<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CityResource;
use App\Models\City;

class AddressResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'distance' => $this->distance,
            'user_id' => $this->user_id,
            'city_id' => $this->city_id,
            'city' => new CityResource(City::find($this->city_id)),
            'description' => $this->description,
            'is_default' => $this->is_default,
        ];
    }
}