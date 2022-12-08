<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'lat', 'lng', 'province_id'];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function provinces() {
        return $this->belongsTo(Province::class);
    }
}
