<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mehradsadeghi\FilterQueryString\FilterQueryString;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;
    use FilterQueryString;

    protected $fillable = ['name', 'description'];

    protected $filters = [
        'like',
    ];


    /**
     * @return belongsToMany
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'books_genres');
    }
}
