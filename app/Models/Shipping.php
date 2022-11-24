<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;
    protected $fillable = ['id_shipping', 'order_id', 'name', 'address', 'phone', 'shipping_on', 'description'];

    /**
     * @return hasOne
     */
    public function orders()
    {
        return $this->hasOne(Order::class);
    }
}