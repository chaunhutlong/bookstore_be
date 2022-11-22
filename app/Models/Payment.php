<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'type', 'status', 'value', 'paid_on', 'description'];
    public $timestamps = false;
    /**
     * @return hasOne
     */
    public function order () {
        return $this->hasOne(Order::class);
    }
}
