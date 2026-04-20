<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách yêu cầu bảo hành</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #eef6f0; color: #0f172a; }
        .wrap { max-width: 1000px; margin: 30px auto; padding: 0 16px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        h1 { margin: 0; font-size: 28px; }
        .link { display: inline-block; color: #0a6640; text-decoration: none; font-weight: 700; }
        .card { background: #fff; border-radius: 18px; padding: 24px; box-shadow: 0 18px 40px rgba(15,23,42,.08); border: 1px solid #d6e4d7; }
        .empty { text-align: center; padding: 40px 20px; color: #51636b; }
        .empty p { margin: 0; font-size: 16px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { text-align: left; padding: 14px; border-bottom: 1px solid #d6e4d7; background: #f8fbf8; font-weight: 700; color: #0a6640; }
        .table td { padding: 14px; border-bottom: 1px solid #d6e4d7; }
        .table tr:hover { background: #f8fbf8; }
        .status { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 999px; font-size: 13px; font-weight: 700; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fecaca; color: #7f1d1d; }
        .order-link { color: #0a6640; text-decoration: none; font-weight: 600; }
        .order-link:hover { text-decoration: underline; }
        .action-btn { display: inline-block; padding: 8px 14px; border-radius: 12px; background: #198754; color: #fff; text-decoration: none; font-weight: 600; font-size: 13px; }
        .action-btn:hover { background: #0a6640; }
        .date { color: #51636b; font-size: 13px; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="header">
            <div>
                <h1>Yêu cầu bảo hành</h1>
            </div>
            <div>
                <a class="link" href="{{ route('warranty.request') }}">+ Tạo yêu cầu mới</a>
                <span style="margin: 0 12px; color: #d6e4d7;">|</span>
                <a class="link" href="/dashboard">← Dashboard</a>
            </div>
        </div>

        <div class="card">
            @if($requests->isEmpty())
                <div class="empty">
                    <p>📭 Bạn chưa gửi yêu cầu bảo hành nào.</p>
                    <p style="margin-top: 8px; font-size: 14px; color: #94a3b8;"><a href="{{ route('warranty.request') }}" style="color: #0a6640; font-weight: 700;">Tạo yêu cầu mới</a></p>
                </div>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mã yêu cầu</th>
                            <th>Đơn hàng</th>
                            <th>Sản phẩm</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td>#{{ $request->id }}</td>
                                <td>{{ $request->order_number }}</td>
                                <td>{{ count($request->products) }} sản phẩm</td>
                                <td>
                                    <span class="status status-{{ $request->status === 'Chờ xử lý' ? 'pending' : ($request->status === 'Đã duyệt' ? 'approved' : 'rejected') }}">
                                        {{ $request->status === 'Chờ xử lý' ? '⏳ Chờ xử lý' : ($request->status === 'Đã duyệt' ? '✓ Đã duyệt' : '✗ Từ chối') }}
                                    </span>
                                </td>
                                <td class="date">{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('warranty.show', $request) }}" class="action-btn">Xem chi tiết</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</body>
</html>
