<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mehradsadeghi\FilterQueryString\FilterQueryString;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'bio', 'address', 'phone', 'email'];

    protected $filters = [
        'like',
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'books_authors')->using(BookAuthor::class);
    }
}
