<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoryController extends Controller
{
    public function index(Request $request): View
    {
        $stories = Story::withTrashed()
            ->with('user')
            ->when($request->query('q'), fn ($q, $term) => $q->where('title', 'like', "%{$term}%"))
            ->when($request->query('show') === 'deleted', fn ($q) => $q->onlyTrashed())
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.stories', ['stories' => $stories]);
    }

    public function destroy(Story $story): RedirectResponse
    {
        $story->delete();

        return back()->with('status', "Deleted “{$story->title}”.");
    }

    public function restore(int $id): RedirectResponse
    {
        $story = Story::onlyTrashed()->findOrFail($id);
        $story->restore();

        return back()->with('status', "Restored “{$story->title}”.");
    }
}
