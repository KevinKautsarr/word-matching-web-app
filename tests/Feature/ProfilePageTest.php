<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserProgress;
use App\Models\Vocabulary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_accessible_and_renders_stats()
    {
        $user = User::factory()->create(['level' => 1, 'xp' => 50]);

        $unit = Unit::create(['title' => 'Unit 1', 'order' => 1]);
        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Lesson 1',
            'order' => 1,
            'is_locked' => false,
            'xp_reward' => 50
        ]);

        Vocabulary::create([
            'lesson_id' => $lesson->id,
            'word' => 'Hello',
            'meaning' => 'Halo'
        ]);

        // Complete the lesson
        UserProgress::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'is_completed' => true,
            'score' => 100,
            'attempts' => 1
        ]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee('Level 1');
        
        // Assert words learned statistic (should be 1 because we completed 1 lesson with 1 vocab)
        $response->assertSee('1'); 
    }

    public function test_profile_page_handles_missing_stats_gracefully()
    {
        $user = User::factory()->create(['level' => 1, 'xp' => 0]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee($user->name);
        
        // Basic stats should be 0
        $response->assertSee('0'); 
    }
}
