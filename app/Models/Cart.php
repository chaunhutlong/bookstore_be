<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;


class Cart extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'book_id', 'quantity', 'price', 'is_checked'];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return belongsTo
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
