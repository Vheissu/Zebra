<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Story;
use App\Models\User;
use App\Models\VoteReason;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_stories_can_be_listed_by_sort(): void
    {
        Story::factory()->create(['title' => 'A link', 'created_at' => now()->subHour()]);
        Story::factory()->ask()->create(['title' => 'Ask: something?']);

        $this->getJson('/api/v1/stories?sort=new')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Ask: something?')
            ->assertJsonPath('data.1.title', 'A link')
            ->assertJsonStructure(['data' => [['id', 'title', 'url', 'domain', 'score', 'comments_count', 'author', 'permalink']], 'links', 'meta']);

        $this->getJson('/api/v1/stories?sort=ask')->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/stories?sort=sideways')->assertUnprocessable();
    }

    public function test_a_story_comes_with_its_comment_tree(): void
    {
        $parent = Comment::factory()->create(['body' => 'Top']);
        Comment::factory()->replyTo($parent)->create(['body' => 'Nested']);

        $this->getJson("/api/v1/stories/{$parent->story_id}")
            ->assertOk()
            ->assertJsonPath('data.comments.0.body', 'Top')
            ->assertJsonPath('data.comments.0.replies.0.body', 'Nested');
    }

    public function test_deleted_comments_hide_their_text_and_author(): void
    {
        $parent = Comment::factory()->create(['body' => 'Regrettable']);
        Comment::factory()->replyTo($parent)->create();
        $parent->delete();

        $this->getJson("/api/v1/stories/{$parent->story_id}")
            ->assertJsonPath('data.comments.0.deleted', true)
            ->assertJsonPath('data.comments.0.body', null)
            ->assertJsonPath('data.comments.0.author', null);
    }

    public function test_tokens_are_issued_for_valid_credentials(): void
    {
        $user = User::factory()->create(['username' => 'botmaker']);

        $token = $this->postJson('/api/v1/tokens', ['username' => 'botmaker', 'password' => 'password', 'name' => 'cli'])
            ->assertCreated()
            ->json('token');

        $this->withToken($token)->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('data.username', 'botmaker')
            ->assertJsonPath('data.email', $user->email);

        $this->postJson('/api/v1/tokens', ['username' => 'botmaker', 'password' => 'wrong', 'name' => 'cli'])->assertUnprocessable();
    }

    public function test_emails_are_private_to_their_owner(): void
    {
        $user = User::factory()->create();

        $this->getJson("/api/v1/users/{$user->username}")->assertOk()->assertJsonMissingPath('data.email');
    }

    public function test_writing_needs_a_token(): void
    {
        $this->postJson('/api/v1/stories', ['title' => 'x', 'url' => 'https://example.com'])->assertUnauthorized();
    }

    public function test_submitting_a_story(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/stories', ['title' => 'Via the API', 'url' => 'https://example.com/api'])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Via the API')
            ->assertJsonPath('data.domain', 'example.com');

        $this->postJson('/api/v1/stories', ['title' => 'Again', 'url' => 'https://example.com/api'])
            ->assertStatus(409)
            ->assertJsonPath('data.title', 'Via the API');
    }

    public function test_commenting_and_replying(): void
    {
        $story = Story::factory()->create();
        Sanctum::actingAs(User::factory()->create());

        $parent = $this->postJson("/api/v1/stories/{$story->id}/comments", ['body' => 'Hello'])->assertCreated()->json('data.id');

        $this->postJson("/api/v1/stories/{$story->id}/comments", ['body' => 'Hello back', 'parent_id' => $parent])
            ->assertCreated()
            ->assertJsonPath('data.parent_id', $parent);
    }

    public function test_voting(): void
    {
        $story = Story::factory()->create();
        $comment = Comment::factory()->create();
        Sanctum::actingAs(User::factory()->withKarma(50)->create());

        $this->postJson("/api/v1/stories/{$story->id}/vote", ['direction' => 'up'])->assertOk()->assertJsonPath('data.score', 2);
        $this->postJson("/api/v1/comments/{$comment->id}/vote", ['direction' => 'down', 'reason' => VoteReason::first()->id])
            ->assertOk()
            ->assertJsonPath('data.score', 0);
    }

    public function test_editing_and_deleting_follow_the_same_rules_as_the_site(): void
    {
        $story = Story::factory()->create();

        Sanctum::actingAs(User::factory()->create());
        $this->patchJson("/api/v1/stories/{$story->id}", ['title' => 'Nope', 'url' => $story->url])->assertForbidden();
        $this->deleteJson("/api/v1/stories/{$story->id}")->assertForbidden();

        Sanctum::actingAs($story->user);
        $this->patchJson("/api/v1/stories/{$story->id}", ['title' => 'Yes', 'url' => $story->url])->assertOk()->assertJsonPath('data.title', 'Yes');
        $this->deleteJson("/api/v1/stories/{$story->id}")->assertNoContent();
    }

    public function test_banned_members_tokens_stop_working(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('bot')->plainTextToken;
        $user->forceFill(['banned_at' => now()])->save();

        $this->withToken($token)->getJson('/api/v1/me')->assertForbidden();
    }

    public function test_vote_reasons_are_listed(): void
    {
        $this->getJson('/api/v1/vote-reasons')->assertOk()->assertJsonCount(10, 'data')->assertJsonPath('data.0.reason', 'Spam');
    }
}
