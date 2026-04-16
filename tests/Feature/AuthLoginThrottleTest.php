<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthLoginThrottleTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_respects_rate_limiting()
    {
        $user = User::factory()->create([
            'email' => 'throttletest@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Hit the endpoint 5 times with wrong password
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => 'throttletest@example.com',
                'password' => 'wrongpassword',
            ]);
            // Depending on implementation, it might just redirect back with error.
        }

        // 6th attempt should be throttled (Too Many Requests - 429)
        $response = $this->post('/login', [
            'email' => 'throttletest@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429);
    }
}
