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
            'image' => $this->image ? asset('storage/' . $this->image) : null ,
            'account_status' => $this->is_private === 'true' ? 'private':'public',
            'role' => $this->role,
            'created_at' => $this->created_at->toDateTimeString(),
            'followers' => $this->followers->count(),
            'following' => $this->following->count()
        ];
    }
}
