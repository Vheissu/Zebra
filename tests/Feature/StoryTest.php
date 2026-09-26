<?php

namespace Tests\Feature;

use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_can_submit_a_link(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/submit', [
            'title' => '  Show Zebra: a news site in a box  ',
            'url' => 'https://www.Example.com/zebra',
        ]);

        $story = Story::sole();
        $response->assertRedirect($story->permalink());
        $this->assertSame('Show Zebra: a news site in a box', $story->title);
        $this->assertSame('show-zebra-a-news-site-in-a-box', $story->slug);
        $this->assertSame('example.com', $story->domain);
        $this->assertSame(1, $story->score);
        $this->assertTrue($story->user->is($user));
    }

    public function test_a_story_needs_a_link_or_text(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/submit', ['title' => 'Nothing here'])
            ->assertSessionHasErrors(['url', 'text']);
    }

    public function test_only_http_links_are_accepted(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/submit', ['title' => 'Sneaky', 'url' => 'javascript:alert(1)'])
            ->assertSessionHasErrors('url');
    }

    public function test_guests_are_sent_to_sign_in(): void
    {
        $this->get('/submit')->assertRedirect('/login');
        $this->post('/submit', ['title' => 'x', 'url' => 'https://example.com'])->assertRedirect('/login');
    }

    public function test_resubmitting_a_recent_link_goes_to_the_existing_discussion(): void
    {
        $existing = Story::factory()->create(['url' => 'https://example.com/post']);

        $this->actingAs(User::factory()->create())
            ->post('/submit', ['title' => 'Same thing', 'url' => 'http://www.example.com/post/?utm_source=x'])
            ->assertRedirect($existing->permalink())
            ->assertSessionHas('status');

        $this->assertSame(1, Story::count());
    }

    public function test_an_old_link_can_be_submitted_again(): void
    {
        Story::factory()->create(['url' => 'https://example.com/post', 'created_at' => now()->subDays(60)]);

        $this->actingAs(User::factory()->create())
            ->post('/submit', ['title' => 'Same thing, a while later', 'url' => 'https://example.com/post']);

        $this->assertSame(2, Story::count());
    }

    public function test_slugs_are_capped_and_never_clash_with_the_edit_url(): void
    {
        $slug = Story::factory()->create(['title' => str_repeat('@', 200)])->slug;

        $this->assertLessThanOrEqual(120, strlen($slug));
        $this->assertStringStartsWith('at-at', $slug);
        $this->assertStringEndsNotWith('-', $slug);
        $this->assertSame('edit-story', Story::factory()->create(['title' => 'Edit'])->slug);
    }

    public function test_story_page_fixes_a_wrong_slug(): void
    {
        $story = Story::factory()->create(['title' => 'Right slug']);

        $this->get("/s/{$story->id}/wrong-slug")->assertRedirect($story->permalink());
        $this->get("/s/{$story->id}")->assertRedirect($story->permalink());
        $this->get($story->permalink())->assertOk()->assertSee('Right slug');
    }

    public function test_titles_and_text_are_escaped(): void
    {
        $story = Story::factory()->ask()->create([
            'title' => '<img src=x onerror=alert(1)>',
            'text' => '<script>alert(2)</script>',
        ]);

        $this->get($story->permalink())
            ->assertOk()
            ->assertDontSee('<img src=x', false)
            ->assertDontSee('<script>alert(2)', false);
    }

    public function test_authors_can_edit_within_the_window(): void
    {
        $story = Story::factory()->create();

        $this->actingAs($story->user)
            ->put("/s/{$story->id}", ['title' => 'Better title', 'url' => $story->url])
            ->assertRedirect();

        $story->refresh();
        $this->assertSame('Better title', $story->title);
        $this->assertSame('better-title', $story->slug);
        $this->assertNotNull($story->edited_at);
    }

    public function test_authors_cannot_edit_after_the_window(): void
    {
        $story = Story::factory()->create(['created_at' => now()->subMinutes(config('zebra.edit_window') + 1)]);

        $this->actingAs($story->user)
            ->put("/s/{$story->id}", ['title' => 'Too late', 'url' => $story->url])
            ->assertForbidden();
    }

    public function test_other_members_cannot_edit_or_delete(): void
    {
        $story = Story::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($other)->get("/s/{$story->id}/edit")->assertForbidden();
        $this->actingAs($other)->delete("/s/{$story->id}")->assertForbidden();
    }

    public function test_admins_can_edit_any_story_at_any_time(): void
    {
        $story = Story::factory()->create(['created_at' => now()->subYear()]);

        $this->actingAs(User::factory()->admin()->create())
            ->put("/s/{$story->id}", ['title' => 'Moderated', 'url' => $story->url])
            ->assertRedirect();

        $this->assertSame('Moderated', $story->fresh()->title);
    }

    public function test_deleting_a_story_hides_it(): void
    {
        $story = Story::factory()->create(['title' => 'Going away']);

        $this->actingAs($story->user)->delete("/s/{$story->id}")->assertRedirect('/');

        $this->assertSoftDeleted($story);
        $this->get('/')->assertDontSee('Going away');
        $this->get($story->permalink())->assertNotFound();
    }

    public function test_domain_and_user_listings(): void
    {
        $user = User::factory()->create(['username' => 'Stripey']);
        Story::factory()->for($user)->create(['title' => 'From example', 'url' => 'https://example.com/1']);
        Story::factory()->create(['title' => 'Elsewhere', 'url' => 'https://other.org/1']);

        $this->get('/from/example.com')->assertSee('From example')->assertDontSee('Elsewhere');
        $this->get('/u/stripey/submissions')->assertSee('From example')->assertDontSee('Elsewhere');
    }

    public function test_listings_paginate(): void
    {
        config(['zebra.per_page' => 2]);
        Story::factory()->count(3)->sequence(
            ['title' => 'Third', 'created_at' => now()->subMinutes(3)],
            ['title' => 'Second', 'created_at' => now()->subMinutes(2)],
            ['title' => 'First', 'created_at' => now()->subMinute()],
        )->create();

        $this->get('/newest')->assertSee('First')->assertSee('Second')->assertDontSee('Third')->assertSee('More');
        $this->get('/newest?page=2')->assertSee('Third')->assertDontSee('Second');
    }
}
