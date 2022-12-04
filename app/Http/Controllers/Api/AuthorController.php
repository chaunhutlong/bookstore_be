<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\AuthorCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $per_page = request()->input('per_page', 10);

            $authors = Author::paginate($per_page);

            return response()->json(new AuthorCollection($authors), 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
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

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'string|required|max:255',
                    'bio' => 'string',
                    'address' => 'string',
                    'phone' => 'string',
                    'email' => 'string|email',
                ]
            );

            $data = $validator->validated();

            $publiser = Author::create($data);
            DB::commit();

            return response()->json(new AuthorResource($publiser), 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function show(Author $author)
    {
        return response()->json(new AuthorResource($author), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Author $author)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'string',
                'bio' => 'string',
                'address' => 'string',
                'phone' => 'string',
                'email' => 'string|email',
            ]);

            $data = $validator->validated();

            $author->update($data);

            DB::commit();
            return response()->json(new AuthorResource($author), 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function destroy(Author $author)
    {
        DB::beginTransaction();
        try {
            $author->delete();
            DB::commit();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }
}
