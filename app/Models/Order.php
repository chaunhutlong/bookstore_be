<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['user_id', 'discount_id', 'payment_id', 'shipping_id', 'status', 'order_on'];

    /**
     * @return belongsTo
     */
    public function payment() {
        return $this->belongsTo(Payment::class);
    }

    /**
     * @return belongsTo
     */
    public function shipping() {
        return $this->belongsTo(Shipping::class);
    }

    /**
     * @return belongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * @return belongsTo
     */
    public function discount() {
        return $this->belongsTo(Discount::class);
    }

    /**
     * @return hasMany
     */
    public function orderDetails() {
        return $this->hasMany(OrderDetail::class);
    }

}
