<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;
    protected $fillable = ['tracking_num', 'order_id', 'address_id', 'shipping_on', 'value', 'description'];

    /**
     * @return hasOne
     */
    public function order()
    {
        return $this->hasOne(Order::class);
    }

    /**
     * @return hasOne
     */
    public function addresses()
    {
        return $this->hasOne(Address::class, 'id', 'address_id');
    }
}