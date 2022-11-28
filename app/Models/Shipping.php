<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'address_id', 'phone', 'value', 'shipping_on'];

    /**
     * @return hasOne
     */
    public function payments () {
        return $this->hasOne(Payment::class);
    }

    public function addresses () {
        return $this->belongsTo(Address::class);
    }
}
