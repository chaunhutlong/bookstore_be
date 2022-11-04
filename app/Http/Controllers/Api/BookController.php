<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = Book::all();
        return response(['books' => BookResource::collection($books), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'string|required|max:255',
                'available_quantity' => 'required|integer',
                'isbn' => 'required|string|max:20',
                'language' => 'required|string|max:25',
                'total_pages' => 'required|integer',
                'price' => 'required|numeric',
                'book_image' => 'required|string',
                'published_date' => 'required|date',
                'publisher_id' => 'required|integer',
            ]);

            $data = $validator->validated();

            $genres = $data['genres'];
            // check genre in table genres
            foreach ($genres as $genre_id) {
                $genre = Genre::where('id', $genre_id)->first();
                if (!$genre) {
                    return response(['error' => 'Genre not found'], 404);
                }
            }

            $book = Book::create($data);
            // add genres to book
            $book->genres()->attach($genres);

            DB::commit();
            return response(['book' => new BookResource($book), 'message' => 'Book created successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        return response(['book' => new BookResource($book), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), ['name' => 'string|required|255']);

            $data = $validator->validated();

            $book->update($data);

            DB::commit();
            return response(['book' => new BookResource($book), 'message' => 'Book updated successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return response(['message' => 'Book deleted successfully']);
    }
}
