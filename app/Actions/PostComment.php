<?php

namespace App\Actions;

use App\Models\Comment;
use App\Models\Story;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PostComment
{
    public static function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:10000'],
        ];
    }

    /**
     * @throws ValidationException
     */
    public function handle(User $user, Story $story, string $body, ?Comment $parent = null): Comment
    {
        Validator::make(['body' => $body], static::rules())->validate();

        if ($story->trashed()) {
            throw ValidationException::withMessages(['body' => 'That story has been deleted.']);
        }

        if ($parent && ($parent->story_id !== $story->id || $parent->trashed())) {
            throw ValidationException::withMessages(['body' => "You can't reply to that comment."]);
        }

        $comment = new Comment(['body' => trim($body)]);
        $comment->story()->associate($story);
        $comment->user()->associate($user);
        $comment->parent()->associate($parent);
        $comment->save();

        return $comment;
    }
}
