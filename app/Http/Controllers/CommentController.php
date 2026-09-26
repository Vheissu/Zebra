<?php

namespace App\Http\Controllers;

use App\Actions\PostComment;
use App\Models\Comment;
use App\Models\Story;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CommentController extends Controller
{
    /**
     * The newest comments across the whole site.
     */
    public function index(Request $request): View
    {
        $comments = Comment::with('user', 'story')
            ->whereHas('story')
            ->latest()
            ->latest('id')
            ->simplePaginate(config('zebra.per_page'));

        return view('comments.index', [
            'comments' => $comments,
            'votes' => $request->user()?->votesOn($comments->items()) ?? collect(),
            'section' => 'comments',
        ]);
    }

    /**
     * A single comment with its replies, and a form to reply to it.
     */
    public function show(Request $request, Comment $comment): View
    {
        $comment->load('user', 'story', 'parent');

        abort_if($comment->story === null, 404);

        $thread = $comment->story->comments()->withTrashed()->with('user')->get();
        $comment->setRelation('children', Comment::tree($thread, $comment->id));

        return view('comments.show', [
            'comment' => $comment,
            'votes' => $request->user()?->votesOn($thread) ?? collect(),
        ]);
    }

    public function store(Request $request, Story $story, PostComment $post): RedirectResponse
    {
        $request->validate(PostComment::rules() + [
            'parent_id' => ['nullable', 'integer'],
        ]);

        $parent = $request->filled('parent_id') ? Comment::findOrFail($request->integer('parent_id')) : null;

        $comment = $post->handle($request->user(), $story, $request->input('body'), $parent);

        return redirect()->to($story->permalink().'#c'.$comment->id);
    }

    public function edit(Comment $comment): View
    {
        Gate::authorize('update', $comment);

        return view('comments.edit', ['comment' => $comment->load('story')]);
    }

    public function update(Request $request, Comment $comment): RedirectResponse
    {
        Gate::authorize('update', $comment);

        $data = $request->validate(PostComment::rules());

        $comment->body = trim($data['body']);

        if ($comment->isDirty()) {
            $comment->edited_at = now();
            $comment->save();
        }

        return redirect()->to($comment->story->permalink().'#c'.$comment->id);
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return redirect()->to($comment->story->permalink())->with('status', 'Comment deleted.');
    }
}
