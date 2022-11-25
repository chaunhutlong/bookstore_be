<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'payment_id', 'status', 'order_on'];

    /**
     * @return belongsTo
     */
    public function payment() {
        return $this->belongsTo(Payment::class);
    }

    /**
     * @return belongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * @return hasMany
     */
    public function orderDetails() {
        return $this->hasMany(OrderDetail::class);
    }

}
