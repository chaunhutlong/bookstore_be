<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $table = 'orders_details';
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
