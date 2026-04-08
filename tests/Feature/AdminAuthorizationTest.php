<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('secret123'),
        ]);
    }

    private function user(): User
    {
        return User::factory()->create([
            'role' => 'user',
            'password' => Hash::make('secret123'),
        ]);
    }

    public function test_user_cannot_access_admin_area(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->get('/admin')
            ->assertStatus(403);
    }

    public function test_admin_can_access_admin_area(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Admin');
    }

    public function test_admin_can_view_user_list(): void
    {
        $admin = $this->admin();
        $user = $this->user();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee($user->email);
    }

    public function test_admin_can_create_user(): void
    {
        $admin = $this->admin();

        $payload = [
            'name' => 'New Person',
            'email' => 'newperson@example.com',
            'password' => 'secret456',
            'role' => 'user',
        ];

        $this->actingAs($admin)
            ->post(route('admin.users.store'), $payload)
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'newperson@example.com',
            'role' => 'user',
        ]);
    }

    public function test_admin_can_update_user_without_changing_password(): void
    {
        $admin = $this->admin();
        $user = $this->user();
        $originalHash = $user->password;

        $payload = [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => 'user',
        ];

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), $payload)
            ->assertRedirect(route('admin.users.index'));

        $user->refresh();

        $this->assertSame('Updated Name', $user->name);
        $this->assertTrue(Hash::check('secret123', $user->password)); // password unchanged
        $this->assertSame($originalHash, $user->password);
    }

    public function test_admin_can_delete_other_user(): void
    {
        $admin = $this->admin();
        $user = $this->user();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
