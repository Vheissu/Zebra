<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function show(User $user): View
    {
        return view('users.show', [
            'user' => $user,
            'storyCount' => $user->stories()->count(),
            'commentCount' => $user->comments()->count(),
        ]);
    }

    public function stories(Request $request, User $user): View
    {
        $stories = $user->stories()->with('user')->newest()->simplePaginate(config('zebra.per_page'));

        return view('stories.index', [
            'stories' => $stories,
            'votes' => $request->user()?->votesOn($stories->items()) ?? collect(),
            'section' => null,
            'title' => "Submissions by {$user->username}",
            'heading' => "Submissions by {$user->username}",
        ]);
    }

    public function comments(Request $request, User $user): View
    {
        $comments = $user->comments()->with('user', 'story')->whereHas('story')
            ->latest()->latest('id')->simplePaginate(config('zebra.per_page'));

        return view('comments.index', [
            'comments' => $comments,
            'votes' => $request->user()?->votesOn($comments->items()) ?? collect(),
            'section' => null,
            'heading' => "Comments by {$user->username}",
        ]);
    }
}
