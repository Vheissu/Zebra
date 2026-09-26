<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Comment */
class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $deleted = $this->trashed();

        return [
            'id' => $this->id,
            'story_id' => $this->story_id,
            'parent_id' => $this->parent_id,
            'author' => $deleted ? null : $this->whenLoaded('user', fn () => $this->user->username),
            'body' => $deleted ? null : $this->body,
            'deleted' => $deleted,
            'score' => $this->score,
            'created_at' => $this->created_at,
            'edited_at' => $this->edited_at,
            'permalink' => $this->permalink(),
            'replies' => CommentResource::collection($this->whenLoaded('children')),
        ];
    }
}
