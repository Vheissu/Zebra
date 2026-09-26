<?php

namespace App\Models\Concerns;

use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Shared behaviour for things people can vote on (stories and comments).
 *
 * Every item starts with a score of 1, the author's implicit upvote, which
 * is never stored as a vote row and never counts towards their karma.
 */
trait Votable
{
    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function isOwnedBy(?User $user): bool
    {
        return $user !== null && $this->user_id === $user->id;
    }

    /**
     * Whether the author may still edit this, per the configured edit window.
     */
    public function isWithinEditWindow(): bool
    {
        return $this->created_at->diffInMinutes(now()) < config('zebra.edit_window');
    }

    /**
     * Apply a change in vote tallies and keep the derived columns in sync.
     */
    public function applyVoteDelta(int $upvotes, int $downvotes): void
    {
        $this->upvotes += $upvotes;
        $this->downvotes += $downvotes;
        $this->score = 1 + $this->upvotes - $this->downvotes;

        $this->refreshRanking();

        $this->saveQuietly();
    }

    /**
     * Hook for models that store a derived ranking value.
     */
    protected function refreshRanking(): void
    {
        //
    }
}
