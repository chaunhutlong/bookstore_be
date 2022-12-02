<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'payment_id', 'status', 'order_on', 'is_deleted', 'deleted_at'];

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
