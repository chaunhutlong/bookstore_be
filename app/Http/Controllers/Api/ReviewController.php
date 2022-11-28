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
     *  @OA\Get(
     *      path="reviews/{book}/",
     *      operationId="getReviewsListByIdBook",
     *      tags={"reviews"},
     *      summary="Get list reviews of a book",
     *      description="Return list reviews of a book",
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

    public function index($book)
    {
        DB::beginTransaction();
        try {
            $reviews = Review::with('user:id,name')->where('book_id', $book)->get();

            if ($reviews) {
                foreach ($reviews as $rev) {
                    $user = User::findOrFail($rev->user_id);
                    $rev->user_name = $user->name;
                }
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
     *  @OA\Get(
     *      path="reviews/{book}/review",
     *      operationId="getReview",
     *      summary="User get review of a book",
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

    public function getReview($book)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            $review = Review::where('user_id', $user->id)->where('book_id', $book)->get();

            foreach ($review as $rev) {
                $rev->user_name = $user->name;
            }

            DB::commit();
            return response([
                'review' => ReviewResource::collection($review),
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
     *  @OA\Post(
     *      path="reviews/{book}/review",
            operationId="createOrUpdateReview",
     *      summary="Create or update review of a book",
     *      description="Returns Created or updated review of a book",
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
     *      ),
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
     *  @OA\Delete(
     *      path="reviews/{book}/{review}",
     *      operationId="deleteReview",
     *      summary="Delete existing review of a book",
     *      description="Delete a record and returns no content",
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
     *          @OA\JsonContent()
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found",
     *      ),
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