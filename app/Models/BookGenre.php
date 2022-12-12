<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookGenre extends Model
{
    use HasFactory;

    protected $table = 'books_genres';
    public $timestamps = false;
    protected $fillable = ['book_id', 'genre_id'];
}
