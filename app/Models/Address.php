<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'city_id', 'distance', 'phone', 'user_id', 'description', 'is_default'];

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

    /**
     * @return HasOne
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}