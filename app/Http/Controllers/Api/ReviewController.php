<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function render(Book $book)
    {
        $user = auth()->user();
        $reviews = Review::where('user_id', $user->id, 'book_id', $book->id)->get();
        return response([
            'reviews' => ReviewResource::collection($reviews),
            'message' => 'Retrieved successfully'
        ], 200);
    }
}