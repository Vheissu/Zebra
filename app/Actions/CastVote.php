<?php

namespace App\Actions;

use App\Models\Comment;
use App\Models\Story;
use App\Models\User;
use App\Models\Vote;
use App\Models\VoteReason;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CastVote
{
    /**
     * Record a user's vote on a story or comment.
     *
     * $value is 1 (upvote), -1 (downvote) or 0 (take the vote back). Casting
     * the opposite vote replaces the previous one. Downvotes need a reason and
     * enough karma. The author's karma moves with every vote they receive.
     *
     * @throws ValidationException
     */
    public function handle(User $user, Story|Comment $item, int $value, ?int $reasonId = null): Story|Comment
    {
        $noun = $item instanceof Story ? 'story' : 'comment';

        if ($item->trashed()) {
            throw ValidationException::withMessages(['vote' => "That {$noun} has been deleted."]);
        }

        if ($item->isOwnedBy($user)) {
            throw ValidationException::withMessages(['vote' => "You can't vote on your own {$noun}."]);
        }

        if ($value === Vote::DOWN) {
            if (! $user->canDownvote()) {
                throw ValidationException::withMessages([
                    'vote' => 'You need '.config('zebra.downvote_karma').' karma before you can downvote.',
                ]);
            }

            if (! $reasonId || ! VoteReason::whereKey($reasonId)->exists()) {
                throw ValidationException::withMessages(['reason' => 'Pick a reason for the downvote.']);
            }
        }

        return DB::transaction(function () use ($user, $item, $value, $reasonId) {
            $item = $item->newQuery()->lockForUpdate()->findOrFail($item->getKey());

            $existing = $item->votes()->where('user_id', $user->id)->lockForUpdate()->first();
            $previous = $existing?->value ?? 0;

            if ($previous === $value) {
                return $item;
            }

            if ($value === 0) {
                $existing->delete();
            } else {
                $item->votes()->updateOrCreate(
                    ['user_id' => $user->id],
                    ['value' => $value, 'vote_reason_id' => $value === Vote::DOWN ? $reasonId : null],
                );
            }

            $item->applyVoteDelta(
                upvotes: ($value === Vote::UP) - ($previous === Vote::UP),
                downvotes: ($value === Vote::DOWN) - ($previous === Vote::DOWN),
            );

            User::whereKey($item->user_id)->increment('karma', $value - $previous);

            return $item;
        });
    }
}
