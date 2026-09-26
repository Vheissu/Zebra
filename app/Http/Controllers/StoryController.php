<?php

namespace App\Http\Controllers;

use App\Actions\SubmitStory;
use App\Models\Comment;
use App\Models\Story;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StoryController extends Controller
{
    public function index(Request $request): View
    {
        return $this->listing($request, Story::query()->hot(), 'hot');
    }

    public function newest(Request $request): View
    {
        return $this->listing($request, Story::query()->newest(), 'newest', 'New');
    }

    public function ask(Request $request): View
    {
        return $this->listing($request, Story::query()->ask()->hot(), 'ask', 'Ask');
    }

    public function domain(Request $request, string $domain): View
    {
        $domain = mb_strtolower($domain);

        return $this->listing($request, Story::query()->where('domain', $domain)->newest(), null, "Stories from {$domain}");
    }

    private function listing(Request $request, $query, ?string $section, ?string $title = null): View
    {
        /** @var Paginator $stories */
        $stories = $query->with('user')->simplePaginate(config('zebra.per_page'));

        return view('stories.index', [
            'stories' => $stories,
            'votes' => $request->user()?->votesOn($stories->items()) ?? collect(),
            'section' => $section,
            'title' => $title,
            'heading' => $section === null ? $title : null,
        ]);
    }

    public function show(Request $request, Story $story, ?string $slug = null): View|RedirectResponse
    {
        if ($slug !== $story->slug) {
            return redirect()->to($story->permalink(), 301);
        }

        $comments = $story->comments()->withTrashed()->with('user')->get();

        return view('stories.show', [
            'story' => $story->load('user'),
            'comments' => Comment::tree($comments),
            'votes' => $request->user()?->votesOn($comments->push($story)) ?? collect(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('stories.create', [
            'prefill' => $request->only('title', 'url'),
        ]);
    }

    public function store(Request $request, SubmitStory $submit): RedirectResponse
    {
        $request->validate(SubmitStory::rules(), SubmitStory::messages());

        if ($duplicate = $submit->duplicateOf($request->input('url'))) {
            return redirect()->to($duplicate->permalink())
                ->with('status', 'That link was already submitted, so here is the discussion.');
        }

        $story = $submit->handle($request->user(), $request->only('title', 'url', 'text'));

        return redirect()->to($story->permalink());
    }

    public function edit(Story $story): View
    {
        Gate::authorize('update', $story);

        return view('stories.edit', ['story' => $story]);
    }

    public function update(Request $request, Story $story): RedirectResponse
    {
        Gate::authorize('update', $story);

        $data = $request->validate(SubmitStory::rules($story), SubmitStory::messages());

        $story->fill([
            'title' => trim($data['title']),
            'url' => $data['url'] ?? null,
            'text' => isset($data['text']) ? trim($data['text']) : null,
        ]);

        if ($story->isDirty()) {
            $story->edited_at = now();
            $story->save();
        }

        return redirect()->to($story->permalink())->with('status', 'Story updated.');
    }

    public function destroy(Story $story): RedirectResponse
    {
        Gate::authorize('delete', $story);

        $story->delete();

        return redirect()->route('home')->with('status', 'Story deleted.');
    }
}
