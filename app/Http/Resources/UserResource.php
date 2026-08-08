<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'image' => $this->path ?? asset('storage/' . $this->image) ,
            'account_status' => $this->is_private === true ? 'private':'public',
            'role' => $this->role,
            'followers' => $this->followers->count(),
            'following' => $this->following->count(),
            'stories' => $this->stories(),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
