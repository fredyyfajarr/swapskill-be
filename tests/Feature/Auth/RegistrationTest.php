<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register(): void
    {
        Storage::fake('public');

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'nim' => '1301209999',
            'whatsapp_number' => '081234567899',
            'ktm' => UploadedFile::fake()->image('ktm.jpg'),
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('user.email', 'test@example.com')
            ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'student',
            'is_verified' => false,
        ]);
    }
}
