<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'value', 'start_date', 'end_date', 'quantity', 'description', 'is_public'];

    /**
     * @return BelongsToMany
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'books_discounts', 'discount_id', 'book_id');
    }

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_discounts', 'discount_id', 'user_id');
    }

    /**
     * @return hasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
