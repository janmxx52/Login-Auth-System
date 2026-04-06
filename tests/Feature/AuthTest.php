<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_user_and_redirects_to_login(): void
    {
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'name' => 'New User',
        ]);
    }

    public function test_register_with_existing_email_shows_error(): void
    {
        // Tạo user với email đã tồn tại
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        // Cố gắng đăng ký với email đó
        $response = $this->from('/register')->post('/register', [
            'name' => 'Another User',
            'email' => 'existing@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        // Kiểm tra redirect về trang register và có lỗi email
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['email']);
        
        // Kiểm tra trong database vẫn chỉ có 1 user với email này
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'existing@example.com',
            'name' => $existingUser->name, 
        ]);
    }

    public function test_register_requires_matching_password_confirmation(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Mismatch User',
            'email' => 'mismatch@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'different',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users', ['email' => 'mismatch@example.com']);
    }

    public function test_login_with_valid_credentials_redirects_to_dashboard(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_invalid_password_shows_error(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrongpass',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_with_too_many_failed_attempts_is_throttled(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
        ]);

        for ($i = 0; $i < 3; $i++) {
            $response = $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrongpass',
            ]);
        }

        $response->assertRedirect();
        $response->assertSessionHasErrors();
        
        $this->assertStringContainsString(
            'Bạn đã nhập sai quá nhiều lần',
            session('errors')->first()
        );

        $this->assertGuest();
    }
}
