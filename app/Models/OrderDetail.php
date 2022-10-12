<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'book_id', 'amount', 'price'];

    /**
     * @return belongsTo
     */
    public function order() {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return belongsTo
     */
    public function book() {
        return $this->belongsTo(Book::class);
    }
}
