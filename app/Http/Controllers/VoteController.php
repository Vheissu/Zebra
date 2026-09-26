<?php

namespace App\Http\Controllers;

use App\Actions\CastVote;
use App\Models\Comment;
use App\Models\Story;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VoteController extends Controller
{
    /**
     * Vote buttons are plain forms; the front end submits them with fetch
     * and asks for JSON, and without JavaScript they redirect back.
     */
    public function store(Request $request, string $type, int $id, CastVote $vote): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down', 'none'])],
            'reason' => ['nullable', 'integer'],
        ]);

        $model = match ($type) {
            'story' => Story::class,
            'comment' => Comment::class,
            default => abort(404),
        };

        $item = $vote->handle(
            $request->user(),
            $model::findOrFail($id),
            ['up' => 1, 'down' => -1, 'none' => 0][$data['direction']],
            $data['reason'] ?? null,
        );

        if ($request->expectsJson()) {
            return response()->json([
                'score' => $item->score,
                'vote' => $data['direction'],
            ]);
        }

        return back();
    }
}
