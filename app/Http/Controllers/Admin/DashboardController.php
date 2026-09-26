<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Story;
use App\Models\User;
use App\Models\Vote;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $week = now()->subWeek();

        return view('admin.dashboard', [
            'totals' => [
                'Stories' => [Story::count(), Story::where('created_at', '>=', $week)->count()],
                'Comments' => [Comment::count(), Comment::where('created_at', '>=', $week)->count()],
                'Members' => [User::count(), User::where('created_at', '>=', $week)->count()],
                'Votes' => [Vote::count(), Vote::where('created_at', '>=', $week)->count()],
            ],
            // Anything picking up downvotes is worth a moderator's look.
            'flagged' => Vote::with('votable', 'reason', 'user')
                ->where('value', Vote::DOWN)
                ->whereHasMorph('votable', [Story::class, Comment::class])
                ->latest()
                ->limit(20)
                ->get(),
        ]);
    }
}
