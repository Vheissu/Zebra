<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\PostComment;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Story;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * GET /api/v1/comments, the newest comments site-wide.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate(['per_page' => ['nullable', 'integer', 'between:1,100']]);

        return CommentResource::collection(
            Comment::with('user')->whereHas('story')->latest()->latest('id')
                ->simplePaginate($request->integer('per_page', config('zebra.per_page')))->withQueryString()
        );
    }

    /**
     * GET /api/v1/comments/{id}, with its replies.
     */
    public function show(Comment $comment): CommentResource
    {
        $thread = $comment->story()->withTrashed()->firstOrFail()->comments()->withTrashed()->with('user')->get();

        return new CommentResource($comment->load('user')->setRelation('children', Comment::tree($thread, $comment->id)));
    }

    /**
     * POST /api/v1/stories/{id}/comments with body and optional parent_id.
     */
    public function store(Request $request, Story $story, PostComment $post): JsonResponse
    {
        $request->validate(PostComment::rules() + ['parent_id' => ['nullable', 'integer']]);

        $parent = $request->filled('parent_id') ? Comment::findOrFail($request->integer('parent_id')) : null;

        $comment = $post->handle($request->user(), $story, $request->input('body'), $parent);

        return (new CommentResource($comment->load('user')))->response()->setStatusCode(201);
    }

    public function update(Request $request, Comment $comment): CommentResource
    {
        Gate::authorize('update', $comment);

        $comment->body = trim($request->validate(PostComment::rules())['body']);

        if ($comment->isDirty()) {
            $comment->edited_at = now();
            $comment->save();
        }

        return new CommentResource($comment->load('user'));
    }

    public function destroy(Comment $comment): Response
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return response()->noContent();
    }
}
