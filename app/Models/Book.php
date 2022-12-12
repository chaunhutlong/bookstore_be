<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mehradsadeghi\FilterQueryString\FilterQueryString;
use Illuminate\Support\Facades\DB;

class Book extends Model
{
    use HasFactory;
    use FilterQueryString;

    protected $fillable = ['name', 'available_quantity', 'isbn', 'language', 'total_pages', 'price', 'book_image', 'description', 'published_date', 'publisher_id'];

    protected $filters = [
        'genre',
        'publisher',
        'author',
        'sort',
        'like',
        'order_by',
        'price',
    ];

    public function order_by($query, $value)
    {
        switch ($value) {
            case 'price':
                return $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                return $query->orderBy('price', 'desc');
                break;
            case 'date':
                return $query->orderBy('published_date', 'asc');
                break;
            case 'date_desc':
                return $query->orderBy('published_date', 'desc');
                break;
            case 'top_selling':
                return $query->join('orders_details', 'books.id', '=', 'orders_details.book_id')
                    ->select('books.*', DB::raw('sum(orders_details.quantity) as total_quantity'))
                    ->groupBy('books.id')
                    ->orderBy('total_quantity', 'desc');
            default:
                return $query;
                break;
        }
    }
    public function price($query, $value)
    {
        $value = explode(',', $value);
        return $query->whereBetween('price', $value);
    }

    public function genre($query, $value)
    {
        $value = explode('_', $value);

        return $query->whereHas('genres', function ($query) use ($value) {
            $query->whereIn('genres.id', $value);
        });
    }

    public function publisher($query, $value)
    {
        return $query->where('publisher_id', $value);
    }

    public function author($query, $value)
    {
        $value = explode('_', $value);

        return $query->whereHas('authors', function ($query) use ($value) {
            $query->whereIn('authors.id', $value);
        });
    }

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
    public function publishers()
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

    /**
     * @return HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
