<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ReviewResource;
use App\Http\Resources\ReviewCollection;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'active'])->except(['getReviews', 'getReviewsByBook']);
    }
    /**
     * Display a listing of the resource.
     * @param Book $book
     * @return \Illuminate\Http\Response
     */

    /**
     *  @OA\Get(
     *      path="reviews/{book}/",
     *      operationId="getReviewsListByIdBook",
     *      tags={"Reviews"},
     *      summary="Get list reviews of a book",
     *      description="Return list reviews of a book",
     *      @OA\Parameter(
     *          name="book",
     *          in="path",
     *          description="review id",
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
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *       ),
     *      )
     */

    public function getReviews()
    {
        try {
            $per_page = request()->input('per_page', 10);

            $reviews = Review::with('user')->paginate($per_page);

            return response()->json(new ReviewCollection($reviews), 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Get(
     *      path="reviews/{book}/review",
     *      operationId="getReviewByIdBook",
     *      tags={"Reviews"},
     *      summary="Get review of a book",
     *      description="Return review of a book",
     *      @OA\Parameter(
     *          name="book",
     *          in="path",
     *           description="Book id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ReviewResource")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *      ),
     *  )
     */

    public function getReviewsByBook(Book $book)
    {
        try {
            $reviews = Review::where('book_id', $book->id)->get();
            return ReviewResource::collection($reviews);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
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
     *      path="/api/reviews/{book}",
     *      summary="Create or update review of a book",
     *      description="Returns Created or updated review of a book",
     *      tags={"Reviews"},
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
     *          @OA\JsonContent(ref="#/components/schemas/CreateOrUpdateReviewRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ReviewResource")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *      ),
     *  )
     */

    public function createOrUpdateReview(Book $book)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make(request()->all(), [
                'rating' => 'required|numeric|min:1|max:5',
                'comment' => 'required|string',
            ]);

            $data = $validator->validated();

            $review = Review::updateOrCreate(
                ['user_id' => auth()->user()->id, 'book_id' => $book->id],
                $data
            );

            DB::commit();
            return response()->json(new ReviewResource($review), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */

    /**
     *  @QA\Delete(
     *      path="/api/reviews/{review}",
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
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */

    public function deleteReview(Review $review)
    {
        DB::beginTransaction();
        try {
            // Check if review belongs to user
            if ($review->user_id != auth()->user()->id) {
                return response()->json(['message' => 'You are not authorized to delete this review'], 403);
            }

            $review->delete();
            DB::commit();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}