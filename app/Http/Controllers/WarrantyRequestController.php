<?php

namespace App\Http\Controllers;

use App\Models\WarrantyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class WarrantyRequestController extends Controller
{
    private function mockOrders(): array
    {
        return [
            'valid_received' => [
                'order_number' => 'DH-2026-001',
                'status' => 'delivered',
                'received_at' => Carbon::now()->subDays(4),
                'return_window_days' => 7,
                'products' => [
                    'cerave_cleanser' => [
                        'name' => 'Sữa rửa mặt CeraVe Foaming',
                        'image' => 'https://via.placeholder.com/200x240/f5e6d3/8b7355?text=Sữa+CeraVe',
                        'price' => '679.000₫',
                    ],
                    'bioderma_micellar' => [
                        'name' => 'Nước tẩy trang Bioderma Sensibio',
                        'image' => 'https://via.placeholder.com/200x240/e8f4f8/3b7ea1?text=Bioderma',
                        'price' => '350.000₫',
                    ],
                    'sunscreen' => [
                        'name' => 'Kem chống nắng Sunplay SPF 50',
                        'image' => 'https://via.placeholder.com/200x240/fff3e0/e57f2c?text=Sunplay',
                        'price' => '82.000₫',
                    ],
                ],
            ],
            'expired' => [
                'order_number' => 'DH-2026-002',
                'status' => 'delivered',
                'received_at' => Carbon::now()->subDays(15),
                'return_window_days' => 7,
                'products' => [
                    'serum_vitamin' => [
                        'name' => 'Serum Vitamin C 20% Tây Ban Nha',
                        'image' => 'https://via.placeholder.com/200x240/fff9e6/fbc02d?text=Vitamin+C',
                        'price' => '450.000₫',
                    ],
                    'sheet_mask' => [
                        'name' => 'Mặt nạ giấy Mediheal Tea Tree',
                        'image' => 'https://via.placeholder.com/200x240/e8f5e9/4caf50?text=Mặt+Nạ',
                        'price' => '45.000₫',
                    ],
                ],
            ],
            'invalid' => [
                'order_number' => 'DH-2026-003',
                'status' => 'cancelled',
                'received_at' => null,
                'return_window_days' => 7,
                'products' => [
                    'moisturizer' => [
                        'name' => 'Kem dưỡng ẩm Cetaphil Rich',
                        'image' => 'https://via.placeholder.com/200x240/f3e5f5/7e57c2?text=Cetaphil',
                        'price' => '320.000₫',
                    ],
                ],
            ],
        ];
    }

    private function getSelectedOrder(Request $request): array
    {
        $orders = $this->mockOrders();
        $key = $request->old('order_key', $request->query('order_key', array_key_first($orders)));

        return $orders[$key] ?? reset($orders);
    }

    private function orderIsExpired(array $order): bool
    {
        if ($order['status'] !== 'delivered' || empty($order['received_at'])) {
            return false;
        }

        return $order['received_at']->diffInDays(Carbon::now()) > $order['return_window_days'];
    }

    private function orderIsInvalid(array $order): bool
    {
        return $order['status'] !== 'delivered';
    }

    public function create(Request $request)
    {
        $orders = $this->mockOrders();
        $selectedOrder = $this->getSelectedOrder($request);

        return view('warranty.request', [
            'orders' => $orders,
            'selectedOrder' => $selectedOrder,
        ]);
    }

    public function store(Request $request)
    {
        $orders = $this->mockOrders();

        $validated = $request->validate([
            'order_key' => ['required', Rule::in(array_keys($orders))],
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['string'],
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'products.required' => 'Vui lòng chọn sản phẩm cần đổi trả',
            'products.min' => 'Vui lòng chọn ít nhất một sản phẩm',
            'reason.required' => 'Vui lòng nhập lý do đổi trả',
            'reason.min' => 'Lý do đổi trả phải có ít nhất 10 ký tự',
        ]);

        $order = $orders[$validated['order_key']];

        if ($this->orderIsInvalid($order)) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['order_key' => 'Đơn hàng không đủ điều kiện đổi trả']);
        }

        if ($this->orderIsExpired($order)) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['order_key' => 'Đơn hàng đã hết thời gian đổi trả']);
        }

        WarrantyRequest::create([
            'user_id' => Auth::id(),
            'order_number' => $order['order_number'],
            'order_status' => $order['status'],
            'products' => array_values($validated['products']),
            'reason' => $validated['reason'],
            'status' => 'Chờ xử lý',
        ]);

        return redirect()->route('warranty.success');
    }

    public function success()
    {
        return view('warranty.success');
    }

    public function index()
    {
        $requests = WarrantyRequest::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('warranty.index', [
            'requests' => $requests,
        ]);
    }

    public function show(WarrantyRequest $warrantyRequest)
    {
        if ($warrantyRequest->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem yêu cầu này.');
        }

        return view('warranty.show', [
            'request' => $warrantyRequest,
        ]);
    }
}
