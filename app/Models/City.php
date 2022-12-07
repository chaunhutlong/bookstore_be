<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'lat', 'lng', 'admin_name'];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}