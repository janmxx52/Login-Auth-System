<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WarrantyRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WarrantyRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_warranty_request_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/warranty/request');

        $response->assertStatus(200);
        $response->assertSee('Yêu cầu đổi trả');
        $response->assertSee('Chọn đơn hàng');
    }

    public function test_valid_order_request_creates_warranty_record_and_redirects(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/warranty/request', [
            'order_key' => 'valid_received',
            'products' => ['Tai nghe không dây'],
            'reason' => 'Sản phẩm bị lỗi phần mềm sau khi nhận.',
        ]);

        $response->assertRedirect('/warranty/success');
        $this->assertDatabaseHas('warranty_requests', [
            'user_id' => $user->id,
            'order_number' => 'DH-2026-001',
            'status' => 'Chờ xử lý',
        ]);
    }

    public function test_expired_order_request_shows_error(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/warranty/request')->post('/warranty/request', [
            'order_key' => 'expired',
            'products' => ['Bàn phím cơ'],
            'reason' => 'Muốn đổi sang sản phẩm khác vì chậm giao.',
        ]);

        $response->assertRedirect('/warranty/request');
        $response->assertSessionHasErrors(['order_key']);
        $this->assertDatabaseMissing('warranty_requests', ['order_number' => 'DH-2026-002']);
    }

    public function test_invalid_order_request_shows_error(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/warranty/request')->post('/warranty/request', [
            'order_key' => 'invalid',
            'products' => ['Củ sạc nhanh'],
            'reason' => 'Đơn hàng bị hủy nên muốn đổi trả.',
        ]);

        $response->assertRedirect('/warranty/request');
        $response->assertSessionHasErrors(['order_key']);
        $this->assertDatabaseMissing('warranty_requests', ['order_number' => 'DH-2026-003']);
    }

    public function test_missing_products_shows_validation_error(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/warranty/request')->post('/warranty/request', [
            'order_key' => 'valid_received',
            'products' => [],
            'reason' => 'Trong vòng đổi trả nhưng không chọn sản phẩm.',
        ]);

        $response->assertRedirect('/warranty/request');
        $response->assertSessionHasErrors(['products']);
    }

    public function test_missing_reason_shows_validation_error(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/warranty/request')->post('/warranty/request', [
            'order_key' => 'valid_received',
            'products' => ['Tai nghe không dây'],
            'reason' => '',
        ]);

        $response->assertRedirect('/warranty/request');
        $response->assertSessionHasErrors(['reason']);
    }

    public function test_authenticated_user_can_view_warranty_requests_list(): void
    {
        $user = User::factory()->create();
        $request1 = WarrantyRequest::create([
            'user_id' => $user->id,
            'order_number' => 'DH-2026-001',
            'order_status' => 'delivered',
            'products' => ['Sữa rửa mặt CeraVe'],
            'reason' => 'Sản phẩm bị lỗi phần mềm.',
            'status' => 'Chờ xử lý',
        ]);

        $response = $this->actingAs($user)->get('/warranty/requests');

        $response->assertStatus(200);
        $response->assertSee('Danh sách yêu cầu bảo hành');
        $response->assertSee($request1->order_number);
    }

    public function test_authenticated_user_can_view_warranty_request_detail(): void
    {
        $user = User::factory()->create();
        $request = WarrantyRequest::create([
            'user_id' => $user->id,
            'order_number' => 'DH-2026-001',
            'order_status' => 'delivered',
            'products' => ['Sữa rửa mặt CeraVe'],
            'reason' => 'Sản phẩm bị lỗi phần mềm.',
            'status' => 'Chờ xử lý',
        ]);

        $response = $this->actingAs($user)->get("/warranty/requests/{$request->id}");

        $response->assertStatus(200);
        $response->assertSee('Chi tiết yêu cầu bảo hành');
        $response->assertSee($request->order_number);
        $response->assertSee($request->reason);
    }

    public function test_user_cannot_view_other_user_warranty_request(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $request = WarrantyRequest::create([
            'user_id' => $user1->id,
            'order_number' => 'DH-2026-001',
            'order_status' => 'delivered',
            'products' => ['Sữa rửa mặt CeraVe'],
            'reason' => 'Sản phẩm bị lỗi.',
            'status' => 'Chờ xử lý',
        ]);

        $response = $this->actingAs($user2)->get("/warranty/requests/{$request->id}");

        $response->assertStatus(403);
    }
}
