<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Http\Resources\GenreResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Genrecontroller extends Controller
{
    public function index()
    {
        $genres = Genre::all();
        return response(['genres' => GenreResource::collection($genres), 'message' => 'Retrieved successfully'], 200);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, ['name' => 'required|max:50']);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $genre = Genre::create($data);

        return response(['genre' => new GenreResource($genre), 'message' => 'Genre created successfully']);
    }

    public function show(Genre $genre)
    {
        return response(['genre' => new GenreResource($genre), 'message' => 'Retrieved successfully'], 200);
    }

    public function update(Request $request, Genre $genre)
    {
        $validator = Validator::make($request->all(), ['name'=> 'required|max:50','description' => 'required|max:255']);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $data = $validator->validated();

        $genre->update($data);

        return response(['genre' => new GenreResource($genre), 'message' => 'Publisher updated successfully'], 202);
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();

        return response(['message' => 'Genre deleted successfully']);
    }
}
