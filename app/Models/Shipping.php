<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'address', 'phone', 'shipping_on'];

    /**
     * @return hasOne
     */
    public function order()
    {
        return $this->hasOne(Order::class);
    }
    public function payments () {
        return $this->hasOne(Payment::class);
    }

    public function addresses () {
        return $this->belongsTo(Address::class);
    }
    /**
     * @return hasOne
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }
}