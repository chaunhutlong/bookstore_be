<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['type', 'status', 'total_book_price' ,'discount_id', 'total', 'paid_on', 'description'];
    public $timestamps = false;
    /**
     * @return hasOne
     */
    public function order () {
        return $this->hasOne(Order::class);
    }

    /**
     * @return hasOne
     */
    public function discount() {
        return $this->hasOne(Discount::class);
    }

    /**
     * @return hasOne
     */
    public function shipping() {
        return $this->hasOne(Shipping::class);
    }
}