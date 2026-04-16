<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Tests\TestCase;

class AuthStreakTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_updates_streak_for_first_time()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
            'last_active_at' => null,
            'streak' => 0,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'streak' => 1,
        ]);
        $this->assertNotNull($user->fresh()->last_active_at);
    }

    public function test_login_continues_streak_if_logged_in_yesterday()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
            'last_active_at' => Carbon::yesterday()->setHour(12),
            'streak' => 3,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'streak' => 4,
        ]);
    }

    public function test_register_triggers_streak_update()
    {
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'newuser@example.com')->first();
        
        $this->assertEquals(1, $user->streak);
        $this->assertNotNull($user->last_active_at);
    }
}
