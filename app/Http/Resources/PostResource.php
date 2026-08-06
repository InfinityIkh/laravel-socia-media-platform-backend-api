<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Redis;

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
            'views' => $this->views()->count() + Redis::scard('post:'.$this->id.':viewers'),
            'likes' => $this->likes()->count() + Redis::scard('post:'.$this->id.':likes'),
            'comments' => $this->comments()->count(),
            'reposts' => $this->reposts()->count() + Redis::scard('post:'.$this->id.':reposts'),
            'saves' => $this->saves()->count() + Redis::scard('post:'.$this->id.':saves'),
            'created_at' => $this->created_at->toDateTimeString(),
            'author' => new UserResource($this->user)
        ];
    }
}
