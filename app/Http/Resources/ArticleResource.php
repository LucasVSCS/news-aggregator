<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'url' => $this->url,
            'image_url' => $this->image_url,
            'author' => $this->author,
            'published_at' => $this->published_at->format('Y-m-d H:i:s'),
            'source' => new SourceResource($this->whenLoaded('source')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
