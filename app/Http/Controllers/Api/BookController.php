<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookCollection;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Author;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $books = Book::with('genres')->paginate($perPage);

        return response()->json(new BookCollection($books), 200);
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
                'book_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'published_date' => 'required|date',
                'publisher_id' => 'required|integer',
                'genres' => 'required|integer',
                'authors' => 'required|integer',
            ]);

            $data = $validator->validated();

            $genres = $data['genres'];
            $authors = $data['authors'];

            // check genre in table genres
            foreach ($genres as $genre_id) {
                $genre = Genre::where('id', $genre_id)->first();
                if (!$genre) {
                    return response(['error' => 'Genre not found'], 404);
                }
            }

            // check author in table authors
            foreach ($authors as $author_id) {
                $author = Author::where('id', $author_id)->first();
                if (!$author) {
                    return response(['error' => 'Author not found'], 404);
                }
            }

            // add book_image to storage
            if ($request->hasFile('book_image') && $request->file('book_image')->isValid()) {
                $bookImage = $request->file('book_image');
                $bookImageName = time() . '.' . $bookImage->getClientOriginalExtension();
                $bookImagePath = $bookImage->storeAs('book_images', $bookImageName, 'public');
                $data['book_image'] = $bookImagePath;
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
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'string|max:255',
                    'available_quantity' => 'integer',
                    'isbn' => 'string|max:20',
                    'language' => 'string|max:25',
                    'total_pages' => 'integer',
                    'price' => 'numeric',
                    'book_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'published_date' => 'date',
                    'publisher_id' => 'integer',
                    'genres' => 'integer',
                ]
            );

            $data = $validator->validated();

            $genres = $data['genres'];

            // check genre in table genres
            foreach ($genres as $genre_id) {
                $genre = Genre::where('id', $genre_id)->first();
                if (!$genre) {
                    return response(['error' => 'Genre not found'], 404);
                }
            }

            // add genres to table book_genre
            $book->genres()->sync($genres);


            // add book_image to storage and delete old book_image
            if ($request->hasFile('book_image') && $request->file('book_image')->isValid()) {
                $bookImage = $request->file('book_image');
                $bookImageName = time() . '.' . $bookImage->getClientOriginalExtension();
                $bookImagePath = $bookImage->storeAs('book_images', $bookImageName, 'public');
                $data['book_image'] = $bookImagePath;
                $oldBookImage = $book->book_image;
                if ($oldBookImage && Storage::disk('public')->exists($oldBookImage)) {
                    Storage::disk('public')->delete($oldBookImage);
                }
            }

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

    public static function reduce($book_id, $quantity) {
        $book = Book::where('id',$book_id);
        $availableQuantity = $book->value('available_quantity');
        $book->update(['available_quantity' => $availableQuantity - $quantity]);
    }
}
