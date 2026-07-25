<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'images' => $this->images(),
            'views' => $this->views()->count(),
            'likes' => $this->likes()->count(),
            'comments' => $this->comments()->count(),
            'reposts' => $this->reposts()->count(),
            'saves' => $this->saves()->count(),
            'created_at' => $this->created_at->toDateTimeString(),
            'author' => new UserResource($this->user)
        ];
    }
}
