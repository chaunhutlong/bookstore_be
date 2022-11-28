<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'city_id', 'distance', 'user_id'];

    /**
     * @return HasMany
     */
    public function shippings()
    {
        return $this->hasMany(Shipping::class);
    }

    /**
     * @return HasOne
     */
    public function cities()
    {
        return $this->belongsTo(City::class);
    }
}
