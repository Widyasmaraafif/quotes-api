<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
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
            'quote' => $this->quote,
            'author' => $this->author ?? 'Unknown',
            'category' => new CategoryResource($this->whenLoaded('category')),
            'likes_count' => $this->likes_count ?? $this->likes()->count(),
            'is_liked' => $this->is_liked ?? ($request->user() ? $this->likes()->where('user_id', $request->user()->id)->exists() : false),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
