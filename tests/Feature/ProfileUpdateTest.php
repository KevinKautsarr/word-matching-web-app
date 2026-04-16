<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_their_profile()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $response = $this->actingAs($user)->put('/profile/update', [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
    }

    public function test_user_can_change_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->put('/profile/update', [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }
}
