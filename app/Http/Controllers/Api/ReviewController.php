<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ReviewResource;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Book $book
     * @return \Illuminate\Http\Response
     */
    public function index($book)
    {
        DB::beginTransaction();
        try {
            $reviews = Review::where('book_id', $book)->get();
            // $name = DB::table('users')->select('name')->where('id', $reviews->user_id)->get();
            // $reviews->load(['user' => function ($query) {
            //     $query->select('name')->where('id', 'user_id');
            // }]);

            DB::commit();
            return response([
                'reviews' => ReviewResource::collection($reviews),
                // 'name' => $name,
                'message' => 'Retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function getReview($book)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            $review = Review::where('user_id', $user->id)->where('book_id', $book)->first();

            return response([
                'reviews' => new ReviewResource($review),
                'message' => 'Retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created or updated resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */

    public function createOrUpdateReview(Request $request, $book)
    {
        DB::beginTransaction();
        try {
            $book = Book::where('id', $book)->first();

            $validator = Validator::make($request->all(), [
                'rating' => 'required|numeric|min:1|max:5',
                'comment' => 'string|max:255',
            ]);

            $data = $validator->validated();

            $review = Review::updateOrCreate(
                [
                    'user_id' => auth()->user()->id,
                    'book_id' => $book->id
                ],
                $data
            );

            DB::commit();
            return response([
                'user_info' => new ReviewResource($review),
                'message' => 'Review created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }
}