<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function update(User $user, Comment $comment): Response
    {
        if ($user->is_admin) {
            return Response::allow();
        }

        if (! $comment->isOwnedBy($user)) {
            return Response::deny("That isn't your comment.");
        }

        return $comment->isWithinEditWindow()
            ? Response::allow()
            : Response::deny('Comments can only be edited for '.config('zebra.edit_window').' minutes after posting.');
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->is_admin || $comment->isOwnedBy($user);
    }
}
