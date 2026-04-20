<?php

namespace App\Http\Controllers;

use App\Models\WarrantyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminWarrantyRequestController extends Controller
{
    public function index(): View
    {
        $requests = WarrantyRequest::with(['user', 'processor'])
            ->latest()
            ->get();

        return view('admin.warranty-requests.index', [
            'requests' => $requests,
        ]);
    }

    public function show(WarrantyRequest $warrantyRequest): View
    {
        $warrantyRequest->load(['user', 'processor']);

        return view('admin.warranty-requests.show', [
            'warrantyRequest' => $warrantyRequest,
        ]);
    }

    public function approve(WarrantyRequest $warrantyRequest): RedirectResponse
    {
        if ($this->requestAlreadyProcessed($warrantyRequest)) {
            return redirect()
                ->route('admin.warranty-requests.show', $warrantyRequest)
                ->withErrors(['status' => 'Yêu cầu đã được xử lý']);
        }

        $warrantyRequest->update([
            'status' => 'Đã chấp nhận',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()
            ->route('admin.warranty-requests.show', $warrantyRequest)
            ->with('status', 'Đã chấp nhận yêu cầu bảo hành');
    }

    public function reject(Request $request, WarrantyRequest $warrantyRequest): RedirectResponse
    {
        if ($this->requestAlreadyProcessed($warrantyRequest)) {
            return redirect()
                ->route('admin.warranty-requests.show', $warrantyRequest)
                ->withErrors(['status' => 'Yêu cầu đã được xử lý']);
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'rejection_reason.required' => 'Vui lòng nhập lý do từ chối',
        ]);

        $warrantyRequest->update([
            'status' => 'Đã từ chối',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()
            ->route('admin.warranty-requests.show', $warrantyRequest)
            ->with('status', 'Đã từ chối yêu cầu bảo hành');
    }

    private function requestAlreadyProcessed(WarrantyRequest $warrantyRequest): bool
    {
        return $warrantyRequest->status !== 'Chờ xử lý';
    }
}
