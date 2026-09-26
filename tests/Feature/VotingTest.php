<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Story;
use App\Models\User;
use App\Models\VoteReason;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VotingTest extends TestCase
{
    use RefreshDatabase;

    private function vote(User $user, string $type, int $id, string $direction, ?int $reason = null)
    {
        return $this->actingAs($user)->postJson("/vote/{$type}/{$id}", array_filter([
            'direction' => $direction,
            'reason' => $reason,
        ]));
    }

    public function test_upvoting_raises_the_score_and_the_authors_karma(): void
    {
        $story = Story::factory()->create();
        $hot = $story->hot;

        $this->vote(User::factory()->create(), 'story', $story->id, 'up')
            ->assertOk()
            ->assertJson(['score' => 2, 'vote' => 'up']);

        $story->refresh();
        $this->assertSame(2, $story->score);
        $this->assertGreaterThan($hot, $story->hot);
        $this->assertSame(1, $story->user->fresh()->karma);
    }

    public function test_voting_twice_the_same_way_counts_once(): void
    {
        $story = Story::factory()->create();
        $voter = User::factory()->create();

        $this->vote($voter, 'story', $story->id, 'up');
        $this->vote($voter, 'story', $story->id, 'up')->assertJson(['score' => 2]);

        $this->assertSame(1, $story->votes()->count());
    }

    public function test_a_vote_can_be_taken_back(): void
    {
        $story = Story::factory()->create();
        $voter = User::factory()->create();

        $this->vote($voter, 'story', $story->id, 'up');
        $this->vote($voter, 'story', $story->id, 'none')->assertJson(['score' => 1]);

        $this->assertSame(0, $story->votes()->count());
        $this->assertSame(0, $story->user->fresh()->karma);
    }

    public function test_switching_from_up_to_down_swings_by_two(): void
    {
        $comment = Comment::factory()->create();
        $voter = User::factory()->withKarma(100)->create();
        $reason = VoteReason::first()->id;

        $this->vote($voter, 'comment', $comment->id, 'up')->assertJson(['score' => 2]);
        $this->vote($voter, 'comment', $comment->id, 'down', $reason)->assertJson(['score' => 0]);

        $comment->refresh();
        $this->assertSame(0, $comment->upvotes);
        $this->assertSame(1, $comment->downvotes);
        $this->assertSame(-1, $comment->user->fresh()->karma);
        $this->assertSame($reason, $comment->votes()->first()->vote_reason_id);
    }

    public function test_people_cannot_vote_on_their_own_posts(): void
    {
        $story = Story::factory()->create();

        $this->vote($story->user, 'story', $story->id, 'up')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['vote' => "You can't vote on your own story."]);
    }

    public function test_downvoting_needs_enough_karma(): void
    {
        config(['zebra.downvote_karma' => 20]);
        $story = Story::factory()->create();
        $reason = VoteReason::first()->id;

        $this->vote(User::factory()->withKarma(19)->create(), 'story', $story->id, 'down', $reason)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['vote' => 'You need 20 karma before you can downvote.']);

        $this->vote(User::factory()->withKarma(20)->create(), 'story', $story->id, 'down', $reason)
            ->assertOk()
            ->assertJson(['score' => 0]);
    }

    public function test_admins_can_downvote_without_karma(): void
    {
        $story = Story::factory()->create();

        $this->vote(User::factory()->admin()->create(), 'story', $story->id, 'down', VoteReason::first()->id)->assertOk();
    }

    public function test_downvotes_need_a_reason(): void
    {
        $story = Story::factory()->create();

        $this->vote(User::factory()->withKarma(100)->create(), 'story', $story->id, 'down')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('reason');
    }

    public function test_guests_cannot_vote(): void
    {
        $story = Story::factory()->create();

        $this->postJson("/vote/story/{$story->id}", ['direction' => 'up'])->assertUnauthorized();
    }

    public function test_voting_works_without_javascript(): void
    {
        $story = Story::factory()->create();

        $this->actingAs(User::factory()->create())
            ->from($story->permalink())
            ->post("/vote/story/{$story->id}", ['direction' => 'up'])
            ->assertRedirect($story->permalink());

        $this->assertSame(2, $story->fresh()->score);
    }

    public function test_deleted_posts_cannot_be_voted_on(): void
    {
        $story = Story::factory()->create();
        $story->delete();

        $this->vote(User::factory()->create(), 'story', $story->id, 'up')->assertNotFound();
    }
}
