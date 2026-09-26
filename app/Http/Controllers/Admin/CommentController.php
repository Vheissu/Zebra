<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function index(Request $request): View
    {
        $comments = Comment::withTrashed()
            ->with(['user', 'story' => fn ($q) => $q->withTrashed()])
            ->when($request->query('q'), fn ($q, $term) => $q->where('body', 'like', "%{$term}%"))
            ->when($request->query('show') === 'deleted', fn ($q) => $q->onlyTrashed())
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.comments', ['comments' => $comments]);
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('status', 'Comment deleted.');
    }

    public function restore(int $id): RedirectResponse
    {
        Comment::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('status', 'Comment restored.');
    }
}
