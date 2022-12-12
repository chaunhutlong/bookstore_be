<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class Statistics extends Model
{
    use HasFactory;
    use FilterQueryString;
    protected $fillable = ['order_date', 'sales', 'total_order', 'quantity', 'profit'];
    public $timestamps = false;
    /**
     * @return belongsToMany
     */
    protected $filters = [
        'date'
    ];
     public function date($query, $value)
     {
        $value= explode(',', $value);
        return $query->whereBetween('order_date', $value);
     }
}
