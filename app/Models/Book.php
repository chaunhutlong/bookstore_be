<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'name', 'available_quantity', 'isbn',
        'language', 'total_pages', 'price', 'book_image',
        'description', 'published_date', 'publisher_id'
    ];

    /**
     * @return BelongsToMany
     */
    public function authors()
    {
        return $this->belongsToMany(Author::class, 'books_authors')->using(BookAuthor::class);
    }

    /**
     * @return BelongsToMany
     */
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'books_genres');
    }

    /**
     * @return BelongsToMany
     */
    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'books_discounts');
    }
    /**
     * @return BelongsTo
     */
    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    /**
     * @return HasMany
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * @return HasMany
     */
    public function carts()
    {
        return $this->hasMany(Cart::class, 'book_id');
    }
}