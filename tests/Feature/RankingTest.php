<?php

namespace Tests\Feature;

use App\Models\Story;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_newer_stories_outrank_older_ones_with_the_same_score(): void
    {
        $old = Story::factory()->create(['created_at' => now()->subDay()]);
        $new = Story::factory()->create();

        $this->assertGreaterThan($old->hot, $new->hot);
    }

    public function test_ten_times_the_score_makes_up_for_one_gravity_period(): void
    {
        $now = Carbon::now();
        $gravity = config('zebra.ranking.gravity');

        $this->assertEqualsWithDelta(
            Story::hotValue(1, $now),
            Story::hotValue(10, $now->copy()->subSeconds($gravity)),
            0.0001,
        );
    }

    public function test_front_page_is_ordered_by_hot_and_new_page_by_time(): void
    {
        $older = Story::factory()->create(['title' => 'Older but popular', 'created_at' => now()->subHours(3)]);
        $older->applyVoteDelta(40, 0);
        $newer = Story::factory()->create(['title' => 'Brand new']);

        $this->get('/')->assertSeeInOrder(['Older but popular', 'Brand new']);
        $this->get('/newest')->assertSeeInOrder(['Brand new', 'Older but popular']);
    }

    public function test_ask_lists_only_text_posts(): void
    {
        Story::factory()->create(['title' => 'A link']);
        Story::factory()->ask()->create(['title' => 'Ask: a question?']);

        $this->get('/ask')->assertSee('Ask: a question?')->assertDontSee('A link');
    }

    public function test_rerank_command_recalculates_after_gravity_changes(): void
    {
        $story = Story::factory()->create(['created_at' => now()->subDay()]);
        $before = $story->hot;

        config(['zebra.ranking.gravity' => 90000]);
        $this->artisan('zebra:rerank')->assertSuccessful();

        $this->assertNotEquals($before, $story->fresh()->hot);
        $this->assertEqualsWithDelta(Story::hotValue(1, $story->created_at), $story->fresh()->hot, 0.0001);
    }
}
