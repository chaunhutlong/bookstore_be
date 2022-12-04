<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use HasFactory;
    public $table = 'orders_details';
    public $timestamps = false;
    protected $fillable = ['order_id', 'book_id', 'quantity', 'price'];

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
