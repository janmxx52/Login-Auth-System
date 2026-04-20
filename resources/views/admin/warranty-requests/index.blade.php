<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý yêu cầu bảo hành</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .wrap { max-width: 1180px; margin: 30px auto; padding: 0 16px; }
        .top { display:flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 16px; }
        .link { text-decoration:none; color:#2563eb; font-weight:700; }
        .card { background:#fff; border-radius:16px; padding:18px; box-shadow:0 12px 30px rgba(15,23,42,0.08); border:1px solid #e5e7eb; }
        .status { display:inline-flex; align-items:center; padding:6px 12px; border-radius:999px; font-size:12px; font-weight:700; }
        .status-pending { background:#fef3c7; color:#92400e; }
        .status-approved { background:#dcfce7; color:#166534; }
        .status-rejected { background:#fee2e2; color:#991b1b; }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:12px 10px; border-bottom:1px solid #e5e7eb; text-align:left; vertical-align:top; }
        th { font-weight:700; color:#334155; background:#f8fafc; }
        .action-btn { display:inline-flex; align-items:center; padding:8px 14px; border-radius:12px; background:#0f766e; color:#fff; text-decoration:none; font-weight:700; }
        .muted { color:#64748b; font-size:13px; }
        .empty { padding:32px 16px; text-align:center; color:#64748b; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="top">
            <div>
                <h1 style="margin:0 0 6px;">Danh sách yêu cầu bảo hành</h1>
                <div class="muted">Admin xem toàn bộ yêu cầu và chọn yêu cầu cần xử lý.</div>
            </div>
            <a class="link" href="{{ route('admin.index') }}">← Trang admin</a>
        </div>

        <div class="card">
            @if ($requests->isEmpty())
                <div class="empty">Chưa có yêu cầu bảo hành nào.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã đơn hàng</th>
                            <th>Khách hàng</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Người xử lý</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $request)
                            <tr>
                                <td>#{{ $request->id }}</td>
                                <td>{{ $request->order_number }}</td>
                                <td>
                                    <strong>{{ $request->user?->name ?? 'N/A' }}</strong><br>
                                    <span class="muted">{{ $request->user?->email ?? 'Không rõ email' }}</span>
                                </td>
                                <td>
                                    <span class="status status-{{ $request->status === 'Chờ xử lý' ? 'pending' : ($request->status === 'Đã chấp nhận' ? 'approved' : 'rejected') }}">
                                        {{ $request->status }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $request->processor?->name ?? 'Chưa xử lý' }}</td>
                                <td><a class="action-btn" href="{{ route('admin.warranty-requests.show', $request) }}">Xem chi tiết</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</body>
</html>
