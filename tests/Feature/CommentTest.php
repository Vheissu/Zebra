<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_can_comment_and_reply(): void
    {
        $story = Story::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->post("/s/{$story->id}/comments", ['body' => 'First!'])->assertRedirect();
        $parent = Comment::sole();

        $this->actingAs($user)->post("/s/{$story->id}/comments", ['body' => 'Replying to myself', 'parent_id' => $parent->id])
            ->assertRedirect();

        $reply = Comment::latest('id')->first();
        $this->assertTrue($reply->parent->is($parent));
        $this->assertSame(2, $story->fresh()->comments_count);
    }

    public function test_replies_must_belong_to_the_same_story(): void
    {
        $elsewhere = Comment::factory()->create();
        $story = Story::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post("/s/{$story->id}/comments", ['body' => 'Wrong thread', 'parent_id' => $elsewhere->id])
            ->assertSessionHasErrors('body');
    }

    public function test_threads_render_nested_and_in_score_order(): void
    {
        $story = Story::factory()->create();
        $low = Comment::factory()->for($story)->create(['body' => 'Low scoring top comment']);
        $high = Comment::factory()->for($story)->create(['body' => 'High scoring top comment']);
        $high->applyVoteDelta(5, 0);
        Comment::factory()->replyTo($high)->create(['body' => 'A reply to the high one']);

        $this->get($story->permalink())->assertSeeInOrder([
            'High scoring top comment',
            'A reply to the high one',
            'Low scoring top comment',
        ]);
    }

    public function test_deleted_comments_keep_their_replies_visible(): void
    {
        $parent = Comment::factory()->create(['body' => 'Parent text']);
        Comment::factory()->replyTo($parent)->create(['body' => 'Child text']);
        $lonely = Comment::factory()->for($parent->story)->create(['body' => 'Nobody replied']);

        $this->actingAs($parent->user)->delete("/c/{$parent->id}");
        $this->actingAs($lonely->user)->delete("/c/{$lonely->id}");

        $this->get($parent->story->permalink())
            ->assertSee('This comment was deleted.')
            ->assertSee('Child text')
            ->assertDontSee('Parent text')
            ->assertDontSee('Nobody replied');

        $this->assertSame(1, $parent->story->fresh()->comments_count);
    }

    public function test_comment_permalink_shows_its_subtree(): void
    {
        $parent = Comment::factory()->create(['body' => 'The parent']);
        $child = Comment::factory()->replyTo($parent)->create(['body' => 'The child']);
        Comment::factory()->for($parent->story)->create(['body' => 'A sibling thread']);

        $this->get("/c/{$parent->id}")
            ->assertOk()
            ->assertSee('The parent')
            ->assertSee('The child')
            ->assertDontSee('A sibling thread');
    }

    public function test_comment_bodies_are_escaped(): void
    {
        $comment = Comment::factory()->create(['body' => '<script>alert(1)</script>']);

        $this->get($comment->story->permalink())->assertDontSee('<script>alert(1)', false);
    }

    public function test_authors_can_edit_their_comments(): void
    {
        $comment = Comment::factory()->create();

        $this->actingAs($comment->user)->put("/c/{$comment->id}", ['body' => 'Edited'])->assertRedirect();

        $this->assertSame('Edited', $comment->fresh()->body);
        $this->assertNotNull($comment->fresh()->edited_at);
    }

    public function test_others_cannot_edit_comments(): void
    {
        $comment = Comment::factory()->create();

        $this->actingAs(User::factory()->create())->put("/c/{$comment->id}", ['body' => 'Hijack'])->assertForbidden();
    }

    public function test_recent_comments_page(): void
    {
        Comment::factory()->create(['body' => 'Fresh remark']);

        $this->get('/comments')->assertOk()->assertSee('Fresh remark');
    }
}
