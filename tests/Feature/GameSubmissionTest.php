<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_manipulate_game_score()
    {
        $user = User::factory()->create();
        $unit = Unit::create(['title' => 'Test Unit', 'order' => 1, 'is_locked' => false]);
        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Test Lesson',
            'order' => 1,
            'is_locked' => false,
            'xp_reward' => 50
        ]);

        // Simulated attack: correct is greater than total
        $response = $this->actingAs($user)
            ->withSession(['game_attempt_' . $lesson->id => ['token' => 'test-token', 'vocab_count' => 10, 'expected_total' => 30, 'started_at' => now()->timestamp]])
            ->postJson("/game/{$lesson->id}/submit", [
                'attempt_token' => 'test-token',
                'score'   => 150, // Keep under 500 to pass first validation block
                'correct' => 15,  // BUT correct > total! (15 > 10)
                'total'   => 10,
            ]);

        $response->assertStatus(422);
        $response->assertJson(['error' => 'Data hasil tidak logis.']);
    }

    public function test_user_cannot_manipulate_total_count()
    {
        $user = User::factory()->create();
        $unit = Unit::create(['title' => 'Test Unit', 'order' => 1, 'is_locked' => false]);
        $lesson = Lesson::create([
            'unit_id' => $unit->id, 'title' => 'Test Lesson', 'order' => 1,
            'is_locked' => false, 'xp_reward' => 50
        ]);

        $response = $this->actingAs($user)
            ->withSession(['game_attempt_' . $lesson->id => ['token' => 'test-token', 'vocab_count' => 10, 'expected_total' => 30, 'started_at' => now()->timestamp]])
            ->postJson("/game/{$lesson->id}/submit", [
                'attempt_token' => 'test-token',
                'score'   => 80,
                'correct' => 8,
                'total'   => 20, // Expected should be 30
            ]);

        $response->assertStatus(422);
        $response->assertJson(['error' => 'Inkonsistensi total pertanyaan.']);
    }

    public function test_user_cannot_reuse_attempt_token()
    {
        $user = User::factory()->create();
        $unit = Unit::create(['title' => 'Test Unit', 'order' => 1, 'is_locked' => false]);
        $lesson = Lesson::create([
            'unit_id' => $unit->id, 'title' => 'Test Lesson', 'order' => 1,
            'is_locked' => false, 'xp_reward' => 50
        ]);

        // Put token in session
        $this->withSession(['game_attempt_' . $lesson->id => ['token' => 'test-token-valid', 'vocab_count' => 10, 'expected_total' => 30, 'started_at' => now()->timestamp]]);

        // First submit should work
        $response1 = $this->actingAs($user)
            ->postJson("/game/{$lesson->id}/submit", [
                'attempt_token' => 'test-token-valid',
                'score'   => 210, // 21 * 10
                'correct' => 21,
                'total'   => 30,
            ]);
        $response1->assertStatus(200);

        // Second submit should fail because token is deleted
        $response2 = $this->actingAs($user)
            ->postJson("/game/{$lesson->id}/submit", [
                'attempt_token' => 'test-token-valid',
                'score'   => 210,
                'correct' => 21,
                'total'   => 30,
            ]);
        $response2->assertStatus(403);
        $response2->assertJson(['error' => 'Sesi game tidak valid atau sudah kadaluarsa. Mencegah duplikasi submit.']);
    }

    public function test_user_can_submit_valid_game_score()
    {
        $user = User::factory()->create();
        $unit = Unit::create(['title' => 'Test Unit', 'order' => 1, 'is_locked' => false]);
        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Test Lesson',
            'order' => 1,
            'is_locked' => false,
            'xp_reward' => 50
        ]);

        $response = $this->actingAs($user)
            ->withSession(['game_attempt_' . $lesson->id => ['token' => 'valid-token', 'vocab_count' => 10, 'expected_total' => 30, 'started_at' => now()->timestamp]])
            ->postJson("/game/{$lesson->id}/submit", [
                'attempt_token' => 'valid-token',
                'score'   => 210,
                'correct' => 21, // 21/30 = 70% passing
                'total'   => 30,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('user_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'is_completed' => true // 8/10 = 80% which is >= 70%
        ]);
    }
}
