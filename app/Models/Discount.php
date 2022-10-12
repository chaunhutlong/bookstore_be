<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'value', 'start_date', 'end_date'];

    /** 
     * @return BelongsToMany
     */
    public function books () {
        return $this->belongsToMany(Book::class, 'books_discounts');
    }

    /** 
     * @return BelongsToMany
     */
    public function users () {
        return $this->belongsToMany(User::class, 'users_discounts');
    }
    
    /**
     * @return hasMany
     */
    public function orders () {
        return $this->hasMany(Order::class);
    }
}
