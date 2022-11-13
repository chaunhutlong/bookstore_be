<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ReviewResource;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\User;
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
    /**
     * @QA/get(
     *      path="/api/books/{book}/reviews",
     *      summary="Get reviews of a book",
     *      description="Return reviews of a book",
     *      tags={"Reviews"},
     *      @OA\Parameter(
     *          name="book",
     *          in="path",
     *          description="Book id",
     *          required=true,
     *          @OA\Schema(
     *          type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ReviewResource")
     *      ),
     *      @QA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @QA\Response(
     *          response=403,
     *          description="Forbidden",
     *       )
     *      )
     */

    public function index($book)
    {
        DB::beginTransaction();
        try {
            $reviews = Review::where('book_id', $book)->get();

            foreach ($reviews as $review) {
                $name = User::select('name')->where('id', $review->user_id)->first();
                $review->user_name = $name->name;
            }

            DB::commit();
            return response([
                'reviews' => ReviewResource::collection($reviews),
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
    /**
     *  @QA/get(
     *      path="/api/books/{book}/review",
     *      summary="Get review of a book",
     *      description="Return review of a book",
     *      tags={"Review"},
     *      @OA\Parameter(
     *          name="book",
     *          in="path",
     *          description="Book id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @QA\JsonContent(ref="#/components/schemas/ReviewResource")
     *      ),
     *      @QA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @QA\Response(
     *          response=403,
     *          description="Forbidden",
     *      )
     *  )
     */

    public function getReview($book)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            $review = Review::where('user_id', $user->id)->where('book_id', $book)->first();

            return response([
                'reviews' => ReviewResource::collection($review),
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
    /**
     *  @QA\Post(
     *      path="/api/books/{book}/review",
     *      summary="Create or update review of a book",
     *      description="Create or update review of a book",
     *      tags={"Review"},
     *      @OA\Parameter(
     *          name="book",
     *          in="path",
     *          description="Book id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/Review")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Review")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *      )
     *  )
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
                    'book_id' => $book->id,
                ],
                $data
            );

            DB::commit();
            return response([
                'review' => new ReviewResource($review),
                'message' => 'Review created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    /**
     *  @QA\Delete(
     *      path="/api/books/{book}/reviews/{review}",
     *      summary="Delete review of a book",
     *      description="Delete review of a book",
     *      tags={"Review"},
     *      @OA\Parameter(
     *          name="book",
     *          in="path",
     *          description="Book id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="review",
     *          in="path",
     *          description="Review id",
     *          required=true,
     *          @OA\Schema(
     *          type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @QA\Response(
     *          response=403,
     *          description="Forbidden",
     *      ),
     *      @QA\Response(
     *          response=404,
     *          description="Resource Not Found",
     *      )
     *  )
     */

    public function destroy($book, $review)
    {
        DB::beginTransaction();
        try {
            $review = Review::where('book_id', $book)->where('id', $review)->first();
            $review->delete();

            DB::commit();
            return response([
                'message' => 'Review deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }
}