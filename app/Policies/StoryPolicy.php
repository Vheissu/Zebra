<?php

namespace App\Policies;

use App\Models\Story;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StoryPolicy
{
    public function update(User $user, Story $story): Response
    {
        if ($user->is_admin) {
            return Response::allow();
        }

        if (! $story->isOwnedBy($user)) {
            return Response::deny("That isn't your story.");
        }

        return $story->isWithinEditWindow()
            ? Response::allow()
            : Response::deny('Stories can only be edited for '.config('zebra.edit_window').' minutes after posting.');
    }

    public function delete(User $user, Story $story): bool
    {
        return $user->is_admin || $story->isOwnedBy($user);
    }
}
