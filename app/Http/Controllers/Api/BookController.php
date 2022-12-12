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
use Kreait\Firebase\Contract\Storage;

class BookController extends Controller
{
    protected $storageUrl;
    protected $bookStorage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
        $this->bookStorage = app('firebase.storage')->getBucket();
        $this->storageUrl = 'books/';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getBooks(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $books = Book::with('reviews', 'orderDetails')->paginate($perPage);

        $books = Book::filter()->paginate($perPage);

        // return image url
        foreach ($books as $book) {
            $bookImage =  $this->storageUrl . $book->book_image;
            $book->book_image = $this->bookStorage->object($bookImage);
            if ($book->book_image->exists()) {
                $book->book_image = $book->book_image->signedUrl(new \DateTime('+1 hour'));
            } else {
                $book->book_image = null;
            }
        }

        // get total quantity of books
        foreach ($books as $book) {
            // calculate total sold of books in order details
            $totalSold = 0;
            foreach ($book->orderDetails as $orderDetail) {
                $totalSold += $orderDetail->quantity;
            }
            $book->total_sold = $totalSold;
        }
        return response()->json(new BookCollection($books), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createBook(Request $request)
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
                'description' => 'required|string',
                'book_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'published_date' => 'required|date',
                'publisher_id' => 'required|integer',
                'genres' => 'required|string',
                'authors' => 'required|string',
            ]);

            $data = $validator->validated();

            $genres = json_decode($data['genres'], true);
            $authors = json_decode($data['authors'], true);

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

            // add book_image to storage firebase
            if ($request->hasFile('book_image') && $request->file('book_image')->isValid()) {
                $bookImage = $request->file('book_image');
                $bookImageName = time() . '.' . $bookImage->getClientOriginalName();
                $this->bookStorage->upload(
                    file_get_contents($bookImage),
                    [
                        'name' => $this->storageUrl . $bookImageName
                    ]
                );

                $data['book_image'] = $bookImageName;
            }

            $book = Book::create($data);
            // add genres to book
            $book->genres()->attach($genres);
            // add authors to book
            $book->authors()->attach($authors);

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
    public function getBook(Book $book)
    {
        // return image url
        $bookImage =  $this->storageUrl . $book->book_image;
        $book->book_image = $this->bookStorage->object($bookImage);
        if ($book->book_image->exists()) {
            $book->book_image = $book->book_image->signedUrl(new \DateTime('+1 hour'));
        } else {
            $book->book_image = null;
        }

        return response()->json(new BookResource($book), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function updateBook(Request $request, Book $book)
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
                    'description' => 'string',
                    'book_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'published_date' => 'date',
                    'publisher_id' => 'integer',
                    'genres' => 'string',
                    'authors' => 'string',
                ]
            );

            $data = $validator->validated();

            // check genre in request
            if (isset($data['genres'])) {
                $genres = json_decode($data['genres']);
                // check genre in table genres
                foreach ($genres as $genre_id) {
                    $genre = Genre::where('id', $genre_id)->first();
                    if (!$genre) {
                        return response(['error' => 'Genre not found'], 404);
                    }
                }

                // add genres to book
                $book->genres()->sync($genres);
            }

            // check author in request
            if (isset($data['authors'])) {
                $authors = json_decode($data['authors'], true);
                // check author in table authors
                foreach ($authors as $author_id) {
                    $author = Author::where('id', $author_id)->first();
                    if (!$author) {
                        return response(['error' => 'Author not found'], 404);
                    }
                }
                $book->authors()->sync($authors);
            }

            // add book_image to storage firebase
            if ($request->hasFile('book_image') && $request->file('book_image')->isValid()) {
                // delete old book_image
                if ($book->book_image) {
                    $oldBookImage = $this->storageUrl . $book->book_image;
                    $bookImageStorage = $this->bookStorage->object($oldBookImage);
                    if ($bookImageStorage->exists()) {
                        $bookImageStorage->delete();
                    }
                }

                $bookImage = $request->file('book_image');
                $bookImageName = time() . '.' . $bookImage->getClientOriginalName();
                $this->bookStorage->upload(
                    file_get_contents($bookImage),
                    [
                        'name' => $this->storageUrl . $bookImageName
                    ]
                );

                $data['book_image'] = $bookImageName;
            }


            $book->update($data);

            DB::commit();
            return response(['book' => new BookResource($book), 'message' => 'Book updated successfully', 'data' => $data]);
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
    public function deleteBook(Book $book)
    {
        // delete in book_image
        if ($book->book_image) {
            $bookImage = $this->storageUrl . $book->book_image;
            // check exist book_image in storage firebase
            $bookImageStorage = $this->bookStorage->object($bookImage);
            if ($bookImageStorage->exists()) {
                $bookImageStorage->delete();
            }
        }

        $book->delete();

        return response(['message' => 'Book deleted successfully']);
    }

    public static function reduce($book_id, $quantity)
    {
        $book = Book::where('id', $book_id);
        $availableQuantity = $book->value('available_quantity');
        $book->update(['available_quantity' => $availableQuantity - $quantity]);
    }
}
