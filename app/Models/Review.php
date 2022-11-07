<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * @return hasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return hasOne
     */
    public function book()
    {
        return $this->hasOne(Book::class, 'id', 'book_id');
    }

    /**
     * @return hasOne
     */
    public function status()
    {
        return $this->hasOne(ReviewStatus::class, 'id', 'status_id');
    }

    /**
     * @return hasOne
     */
    
}
