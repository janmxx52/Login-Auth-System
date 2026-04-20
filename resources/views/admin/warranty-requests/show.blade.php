<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết yêu cầu bảo hành</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .wrap { max-width: 960px; margin: 30px auto; padding: 0 16px; }
        .link { text-decoration:none; color:#2563eb; font-weight:700; }
        .card { background:#fff; border-radius:16px; padding:22px; box-shadow:0 12px 30px rgba(15,23,42,0.08); border:1px solid #e5e7eb; margin-top:16px; }
        .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
        .info { padding:14px; background:#f8fafc; border:1px solid #e5e7eb; border-radius:12px; }
        .label { font-size:12px; font-weight:700; text-transform:uppercase; color:#0f766e; letter-spacing:.04em; }
        .value { margin-top:6px; font-weight:600; }
        .status { display:inline-flex; align-items:center; padding:6px 12px; border-radius:999px; font-size:12px; font-weight:700; }
        .status-pending { background:#fef3c7; color:#92400e; }
        .status-approved { background:#dcfce7; color:#166534; }
        .status-rejected { background:#fee2e2; color:#991b1b; }
        .products { display:grid; gap:10px; margin-top:14px; }
        .product { padding:12px 14px; background:#f8fafc; border-left:4px solid #0f766e; border-radius:12px; }
        .flash { margin-top:16px; padding:12px 14px; border-radius:12px; }
        .flash-success { background:#ecfdf5; color:#166534; border:1px solid #bbf7d0; }
        .flash-error { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; }
        .actions { display:flex; gap:12px; flex-wrap:wrap; margin-top:18px; }
        .btn { border:none; border-radius:12px; padding:12px 16px; font-weight:700; cursor:pointer; }
        .btn-approve { background:#166534; color:#fff; }
        .btn-reject { background:#b91c1c; color:#fff; }
        textarea { width:100%; min-height:120px; margin-top:10px; padding:12px; border:1px solid #cbd5e1; border-radius:12px; font-size:14px; resize:vertical; }
        .muted { color:#64748b; font-size:14px; }
    </style>
</head>
<body>
    <div class="wrap">
        <a class="link" href="{{ route('admin.warranty-requests.index') }}">← Danh sách yêu cầu</a>

        @if (session('status'))
            <div class="flash flash-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="flash flash-error">
                @foreach ($errors->all() as $error)
                    • {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <div class="card">
            <h1 style="margin-top:0;">Chi tiết yêu cầu bảo hành #{{ $warrantyRequest->id }}</h1>
            <p class="muted">Admin xem thông tin yêu cầu, sản phẩm, lý do và thao tác xử lý tại đây.</p>

            <div class="grid" style="margin-top:18px;">
                <div class="info">
                    <div class="label">Mã đơn hàng</div>
                    <div class="value">{{ $warrantyRequest->order_number }}</div>
                </div>
                <div class="info">
                    <div class="label">Khách hàng</div>
                    <div class="value">{{ $warrantyRequest->user?->name ?? 'N/A' }}</div>
                    <div class="muted">{{ $warrantyRequest->user?->email ?? 'Không rõ email' }}</div>
                </div>
                <div class="info">
                    <div class="label">Trạng thái</div>
                    <div class="value">
                        <span class="status status-{{ $warrantyRequest->status === 'Chờ xử lý' ? 'pending' : ($warrantyRequest->status === 'Đã chấp nhận' ? 'approved' : 'rejected') }}">
                            {{ $warrantyRequest->status }}
                        </span>
                    </div>
                </div>
                <div class="info">
                    <div class="label">Ngày tạo</div>
                    <div class="value">{{ $warrantyRequest->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="info">
                    <div class="label">Người xử lý</div>
                    <div class="value">{{ $warrantyRequest->processor?->name ?? 'Chưa xử lý' }}</div>
                </div>
                <div class="info">
                    <div class="label">Thời gian xử lý</div>
                    <div class="value">{{ $warrantyRequest->processed_at?->format('d/m/Y H:i') ?? 'Chưa xử lý' }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 style="margin-top:0;">Sản phẩm cần đổi trả</h2>
            <div class="products">
                @foreach ($warrantyRequest->products as $product)
                    <div class="product">{{ $product }}</div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <h2 style="margin-top:0;">Lý do yêu cầu</h2>
            <div class="product" style="border-left-color:#2563eb;">{{ $warrantyRequest->reason }}</div>

            @if ($warrantyRequest->rejection_reason)
                <h2 style="margin:18px 0 0;">Lý do từ chối</h2>
                <div class="product" style="border-left-color:#b91c1c;">{{ $warrantyRequest->rejection_reason }}</div>
            @endif
        </div>

        <div class="card">
            <h2 style="margin-top:0;">Xử lý yêu cầu</h2>

            @if ($warrantyRequest->status === 'Chờ xử lý')
                <div class="actions">
                    <form method="POST" action="{{ route('admin.warranty-requests.approve', $warrantyRequest) }}">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-approve" type="submit">Chấp nhận yêu cầu</button>
                    </form>
                </div>

                <form method="POST" action="{{ route('admin.warranty-requests.reject', $warrantyRequest) }}" style="margin-top:16px;">
                    @csrf
                    @method('PATCH')
                    <label for="rejection_reason" class="label" style="display:block;">Lý do từ chối</label>
                    <textarea id="rejection_reason" name="rejection_reason" placeholder="Nhập lý do từ chối..." required>{{ old('rejection_reason') }}</textarea>
                    <div class="actions">
                        <button class="btn btn-reject" type="submit">Từ chối yêu cầu</button>
                    </div>
                </form>
            @else
                <p class="muted">Yêu cầu này đã được xử lý và không thể xử lý lại.</p>
            @endif
        </div>
    </div>
</body>
</html>
