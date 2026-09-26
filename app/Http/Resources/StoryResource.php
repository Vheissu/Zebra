<?php

namespace App\Http\Resources;

use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Story */
class StoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->url,
            'domain' => $this->domain,
            'text' => $this->text,
            'score' => $this->score,
            'comments_count' => $this->comments_count,
            'author' => $this->whenLoaded('user', fn () => $this->user->username),
            'created_at' => $this->created_at,
            'edited_at' => $this->edited_at,
            'permalink' => $this->permalink(),
            'comments' => CommentResource::collection($this->whenLoaded('tree')),
        ];
    }
}
