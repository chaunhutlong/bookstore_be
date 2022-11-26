<?php

namespace App\Http\Resources;

use App\Models\Publisher;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'available_quantity' => $this->available_quantity,
            'book_image' => $this->book_image,
            'isbn' => $this->isbn,
            'language' => $this->language,
            'total_pages' => $this->total_pages,
            'price' => $this->price,
            'published_date' => $this->published_date,
            'publisher' => new PublisherResource(Publisher::find($this->publisher_id)),
            'author' => AuthorResource::collection($this->authors),
            'genres' => GenreResource::collection($this->genres),
            'book_image' => $this->book_image,
            'description' => $this->description,
        ];
    }
}
