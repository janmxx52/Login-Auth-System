<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\WarrantyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $requestCount = WarrantyRequest::where('user_id', $user->id)->count();
        $pendingCount = WarrantyRequest::where('user_id', $user->id)
            ->where('status', 'Chờ xử lý')
            ->count();
        $completedCount = WarrantyRequest::where('user_id', $user->id)
            ->where('status', '<>', 'Chờ xử lý')
            ->count();
        $recentRequests = WarrantyRequest::where('user_id', $user->id)
            ->latest()
            ->take(4)
            ->get();

        return view('dashboard', compact(
            'user',
            'requestCount',
            'pendingCount',
            'completedCount',
            'recentRequests'
        ));
    }
}
