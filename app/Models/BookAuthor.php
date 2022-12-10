<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BookAuthor extends Pivot
{
    use HasFactory;

    protected $table = 'books_authors';
    protected $fillable = ['book_id', 'author_id'];
    public $timestamps = false;
}