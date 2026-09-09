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
            'images' => $this->images()->get(),
            'views' => $this->withCount('views') + Redis::scard('post:'.$this->id.':viewers'),
            'likes' => $this->withCount('likes') + Redis::scard('post:'.$this->id.':likes'),
            'comments' => $this->withCount('comments'),
            'reposts' => $this->withCount('repots') + Redis::scard('post:'.$this->id.':reposts'),
            'saves' => $this->withCount('saves') + Redis::scard('post:'.$this->id.':saves'),
            'hashtags' => $this->hashtags()->pluck('hashtag'),
            'created_at' => $this->created_at->toDateTimeString(),
            'author' => new UserResource($this->user)
        ];
    }
}
