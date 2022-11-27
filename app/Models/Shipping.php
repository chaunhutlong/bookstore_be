<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;
    protected $fillable = ['tracking_num', 'order_id', 'name', 'address', 'phone', 'shipping_on', 'shipping_fee', 'description'];

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
    public function user()
    {
        return $this->hasOne(User::class);
    }
}