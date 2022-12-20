<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mehradsadeghi\FilterQueryString\FilterQueryString;
use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'address', 'phone', 'email', 'description'];

    protected $filters = [
        'like',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * @return hasMany
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
