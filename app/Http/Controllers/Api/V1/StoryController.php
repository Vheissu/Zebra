<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\SubmitStory;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoryResource;
use App\Models\Comment;
use App\Models\Story;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoryController extends Controller
{
    /**
     * GET /api/v1/stories?sort=hot|new|ask&per_page=30&page=1
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'sort' => ['nullable', Rule::in(['hot', 'new', 'ask'])],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ]);

        $stories = match ($request->query('sort', 'hot')) {
            'new' => Story::query()->newest(),
            'ask' => Story::query()->ask()->hot(),
            default => Story::query()->hot(),
        };

        return StoryResource::collection(
            $stories->with('user')->simplePaginate($request->integer('per_page', config('zebra.per_page')))->withQueryString()
        );
    }

    /**
     * GET /api/v1/stories/{id}, with the full comment tree.
     */
    public function show(Story $story): StoryResource
    {
        $comments = $story->comments()->withTrashed()->with('user')->get();

        return new StoryResource($story->load('user')->setRelation('tree', Comment::tree($comments)));
    }

    /**
     * POST /api/v1/stories. A link posted recently is answered with 409 and the existing story.
     */
    public function store(Request $request, SubmitStory $submit): JsonResponse
    {
        $request->validate(SubmitStory::rules(), SubmitStory::messages());

        if ($duplicate = $submit->duplicateOf($request->input('url'))) {
            return response()->json([
                'message' => 'That link was already submitted.',
                'data' => new StoryResource($duplicate->load('user')),
            ], 409);
        }

        $story = $submit->handle($request->user(), $request->only('title', 'url', 'text'));

        return (new StoryResource($story->load('user')))->response()->setStatusCode(201);
    }

    public function update(Request $request, Story $story): StoryResource
    {
        Gate::authorize('update', $story);

        $data = $request->validate(SubmitStory::rules($story), SubmitStory::messages());

        $story->fill($data);

        if ($story->isDirty()) {
            $story->edited_at = now();
            $story->save();
        }

        return new StoryResource($story->load('user'));
    }

    public function destroy(Story $story): Response
    {
        Gate::authorize('delete', $story);

        $story->delete();

        return response()->noContent();
    }
}
