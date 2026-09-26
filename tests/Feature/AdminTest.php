<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admins_can_see_moderation(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();

        $admin = User::factory()->admin()->create();
        foreach (['/admin', '/admin/stories', '/admin/comments', '/admin/users'] as $page) {
            $this->actingAs($admin)->get($page)->assertOk();
        }
    }

    public function test_admins_can_delete_and_restore_stories(): void
    {
        $admin = User::factory()->admin()->create();
        $story = Story::factory()->create();

        $this->actingAs($admin)->delete("/admin/stories/{$story->id}")->assertRedirect();
        $this->assertSoftDeleted($story);

        $this->actingAs($admin)->get('/admin/stories?show=deleted')->assertSee($story->title);

        $this->actingAs($admin)->post("/admin/stories/{$story->id}/restore")->assertRedirect();
        $this->assertNotSoftDeleted($story);
    }

    public function test_admins_can_delete_and_restore_comments(): void
    {
        $admin = User::factory()->admin()->create();
        $comment = Comment::factory()->create();

        $this->actingAs($admin)->delete("/admin/comments/{$comment->id}");
        $this->assertSame(0, $comment->story->fresh()->comments_count);

        $this->actingAs($admin)->post("/admin/comments/{$comment->id}/restore");
        $this->assertSame(1, $comment->story->fresh()->comments_count);
    }

    public function test_banning_revokes_api_tokens_and_unbanning_restores_access(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();
        $member->createToken('bot');

        $this->actingAs($admin)->post("/admin/users/{$member->username}/ban")->assertRedirect();
        $this->assertTrue($member->fresh()->isBanned());
        $this->assertSame(0, $member->tokens()->count());

        $this->actingAs($admin)->post("/admin/users/{$member->username}/unban");
        $this->assertFalse($member->fresh()->isBanned());
    }

    public function test_admins_cannot_ban_or_demote_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post("/admin/users/{$admin->username}/ban")->assertSessionHasErrors('admin');
        $this->actingAs($admin)->post("/admin/users/{$admin->username}/admin")->assertSessionHasErrors('admin');

        $this->assertFalse($admin->fresh()->isBanned());
        $this->assertTrue($admin->fresh()->is_admin);
    }

    public function test_admins_can_promote_members(): void
    {
        $member = User::factory()->create();

        $this->actingAs(User::factory()->admin()->create())->post("/admin/users/{$member->username}/admin");

        $this->assertTrue($member->fresh()->is_admin);
    }
}
