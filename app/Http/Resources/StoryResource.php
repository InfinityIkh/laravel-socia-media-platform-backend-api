<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
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
            'media_path' => $this->media_path,
            'media_type' => $this->media_type,
            'views' => $this->users()->count(),
            'expires_at' => $this->expires_at,
            'author' => $this->user()->get()
        ];
    }
}
