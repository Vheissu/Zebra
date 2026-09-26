<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CastVote;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Story;
use App\Models\VoteReason;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VoteController extends Controller
{
    /**
     * POST /api/v1/stories/{id}/vote or /api/v1/comments/{id}/vote
     * with direction up|down|none and, for downvotes, a reason id.
     */
    public function story(Request $request, Story $story, CastVote $vote): JsonResponse
    {
        return $this->cast($request, $story, $vote);
    }

    public function comment(Request $request, Comment $comment, CastVote $vote): JsonResponse
    {
        return $this->cast($request, $comment, $vote);
    }

    /**
     * GET /api/v1/vote-reasons, the reasons a downvote can be given for.
     */
    public function reasons(): JsonResponse
    {
        return response()->json([
            'data' => VoteReason::ordered()->get(['id', 'reason']),
        ]);
    }

    private function cast(Request $request, Story|Comment $item, CastVote $vote): JsonResponse
    {
        $data = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down', 'none'])],
            'reason' => ['nullable', 'integer'],
        ]);

        $item = $vote->handle(
            $request->user(),
            $item,
            ['up' => 1, 'down' => -1, 'none' => 0][$data['direction']],
            $data['reason'] ?? null,
        );

        return response()->json(['data' => ['score' => $item->score, 'vote' => $data['direction']]]);
    }
}
