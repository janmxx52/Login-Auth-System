<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WarrantyRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminWarrantyRequestTest extends TestCase
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

    private function warrantyRequest(array $attributes = []): WarrantyRequest
    {
        return WarrantyRequest::create(array_merge([
            'user_id' => $this->user()->id,
            'order_number' => 'DH-2026-001',
            'order_status' => 'delivered',
            'products' => ['Sữa rửa mặt CeraVe'],
            'reason' => 'Sản phẩm bị lỗi sau khi nhận hàng.',
            'status' => 'Chờ xử lý',
        ], $attributes));
    }

    public function test_admin_can_view_warranty_request_list(): void
    {
        $admin = $this->admin();
        $request = $this->warrantyRequest();

        $this->actingAs($admin)
            ->get(route('admin.warranty-requests.index'))
            ->assertOk()
            ->assertSee('Danh sách yêu cầu bảo hành')
            ->assertSee($request->order_number)
            ->assertSee($request->user->name)
            ->assertSee($request->status);
    }

    public function test_admin_can_view_warranty_request_detail(): void
    {
        $admin = $this->admin();
        $request = $this->warrantyRequest([
            'products' => ['Sữa rửa mặt CeraVe', 'Kem chống nắng Sunplay'],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.warranty-requests.show', $request))
            ->assertOk()
            ->assertSee('Chi tiết yêu cầu bảo hành')
            ->assertSee($request->reason)
            ->assertSee('Sữa rửa mặt CeraVe')
            ->assertSee('Kem chống nắng Sunplay')
            ->assertSee($request->status);
    }

    public function test_non_admin_cannot_access_admin_warranty_request_pages(): void
    {
        $user = $this->user();
        $request = $this->warrantyRequest();

        $this->actingAs($user)
            ->get(route('admin.warranty-requests.index'))
            ->assertStatus(403);

        $this->actingAs($user)
            ->get(route('admin.warranty-requests.show', $request))
            ->assertStatus(403);
    }

    public function test_admin_can_approve_pending_warranty_request(): void
    {
        $admin = $this->admin();
        $request = $this->warrantyRequest();

        $this->actingAs($admin)
            ->patch(route('admin.warranty-requests.approve', $request))
            ->assertRedirect(route('admin.warranty-requests.show', $request));

        $request->refresh();

        $this->assertSame('Đã chấp nhận', $request->status);
        $this->assertSame($admin->id, $request->processed_by);
        $this->assertNotNull($request->processed_at);
    }

    public function test_admin_can_reject_pending_warranty_request_with_reason(): void
    {
        $admin = $this->admin();
        $request = $this->warrantyRequest();

        $this->actingAs($admin)
            ->patch(route('admin.warranty-requests.reject', $request), [
                'rejection_reason' => 'Đơn hàng không đủ điều kiện bảo hành theo chính sách.',
            ])
            ->assertRedirect(route('admin.warranty-requests.show', $request));

        $request->refresh();

        $this->assertSame('Đã từ chối', $request->status);
        $this->assertSame($admin->id, $request->processed_by);
        $this->assertNotNull($request->processed_at);
        $this->assertSame('Đơn hàng không đủ điều kiện bảo hành theo chính sách.', $request->rejection_reason);
    }

    public function test_reject_requires_reason(): void
    {
        $admin = $this->admin();
        $request = $this->warrantyRequest();

        $this->actingAs($admin)
            ->from(route('admin.warranty-requests.show', $request))
            ->patch(route('admin.warranty-requests.reject', $request), [
                'rejection_reason' => '',
            ])
            ->assertRedirect(route('admin.warranty-requests.show', $request))
            ->assertSessionHasErrors(['rejection_reason']);

        $request->refresh();

        $this->assertSame('Chờ xử lý', $request->status);
        $this->assertNull($request->processed_at);
        $this->assertNull($request->rejection_reason);
    }

    public function test_processed_request_cannot_be_processed_again(): void
    {
        $admin = $this->admin();
        $request = $this->warrantyRequest([
            'status' => 'Đã chấp nhận',
            'processed_by' => $admin->id,
            'processed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.warranty-requests.approve', $request))
            ->assertRedirect(route('admin.warranty-requests.show', $request))
            ->assertSessionHasErrors(['status']);

        $request->refresh();

        $this->assertSame('Đã chấp nhận', $request->status);
    }

    public function test_rejected_request_cannot_be_rejected_again(): void
    {
        $admin = $this->admin();
        $request = $this->warrantyRequest([
            'status' => 'Đã từ chối',
            'processed_by' => $admin->id,
            'processed_at' => now(),
            'rejection_reason' => 'Đã xử lý trước đó.',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.warranty-requests.reject', $request), [
                'rejection_reason' => 'Thử từ chối lại.',
            ])
            ->assertRedirect(route('admin.warranty-requests.show', $request))
            ->assertSessionHasErrors(['status']);

        $request->refresh();

        $this->assertSame('Đã từ chối', $request->status);
        $this->assertSame('Đã xử lý trước đó.', $request->rejection_reason);
    }
}
