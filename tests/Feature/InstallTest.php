<?php

namespace Tests\Feature;

use App\Models\Story;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_builds_a_working_front_page(): void
    {
        $this->seed(DemoSeeder::class);

        $this->assertSame(24, Story::count());
        $this->assertTrue(User::whereUsername('zebra')->sole()->is_admin);

        $this->get('/')->assertOk()->assertSee('How We Deploy At Github');
        $this->get('/ask')->assertOk()->assertSee('Ask: How do you find clients?');
    }
}
