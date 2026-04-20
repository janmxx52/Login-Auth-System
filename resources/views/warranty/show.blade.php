<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết yêu cầu bảo hành</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #eef6f0; color: #0f172a; }
        .wrap { max-width: 900px; margin: 30px auto; padding: 0 16px; }
        .link { display: inline-block; color: #0a6640; text-decoration: none; font-weight: 700; margin-bottom: 24px; }
        .header { margin-bottom: 24px; }
        h1 { margin: 0; font-size: 28px; }
        p { margin: 8px 0 0; color: #51636b; }
        .card { background: #fff; border-radius: 18px; padding: 24px; box-shadow: 0 18px 40px rgba(15,23,42,.08); border: 1px solid #d6e4d7; margin-bottom: 16px; }
        .section-title { font-size: 18px; font-weight: 700; margin: 0 0 16px; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px; margin-bottom: 20px; }
        .info-item { padding: 14px; background: #f8fbf8; border-radius: 12px; border: 1px solid #d6e4d7; }
        .info-label { font-size: 13px; font-weight: 700; color: #0a6640; text-transform: uppercase; letter-spacing: 0.05em; }
        .info-value { font-size: 16px; margin-top: 6px; font-weight: 600; }
        .status { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 999px; font-weight: 700; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fecaca; color: #7f1d1d; }
        .products-list { display: grid; gap: 12px; }
        .product-item { padding: 14px; background: #f8fbf8; border-radius: 12px; border-left: 4px solid #198754; }
        .product-name { font-weight: 600; margin: 0; color: #0f172a; }
        .reason-box { padding: 16px; background: #f8fbf8; border-radius: 12px; border-left: 4px solid #0a6640; }
        .reason-label { font-size: 13px; font-weight: 700; color: #0a6640; text-transform: uppercase; }
        .reason-text { margin-top: 8px; line-height: 1.6; color: #0f172a; }
        .actions { display: flex; gap: 12px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #d6e4d7; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 12px 18px; border-radius: 12px; text-decoration: none; font-weight: 600; border: none; cursor: pointer; font-size: 14px; }
        .btn-primary { background: #198754; color: #fff; }
        .btn-primary:hover { background: #0a6640; }
        .btn-secondary { background: #f8fbf8; color: #0a6640; border: 1px solid #d6e4d7; }
        .btn-secondary:hover { background: #ecfdf5; }
        .timeline { margin-top: 20px; padding-top: 20px; border-top: 1px solid #d6e4d7; }
        .timeline-item { display: flex; gap: 12px; margin-bottom: 14px; }
        .timeline-dot { width: 10px; height: 10px; background: #198754; border-radius: 50%; margin-top: 4px; flex-shrink: 0; }
        .timeline-content { flex: 1; }
        .timeline-date { font-size: 13px; color: #51636b; font-weight: 600; }
        .timeline-text { font-size: 14px; color: #0f172a; }
    </style>
</head>
<body>
    <div class="wrap">
        <a class="link" href="{{ route('warranty.index') }}">← Quay lại danh sách</a>

        <div class="header">
            <h1>Chi tiết yêu cầu bảo hành #{{ $request->id }}</h1>
            <p>Mã yêu cầu: <strong>{{ $request->id }}</strong></p>
        </div>

        <div class="card">
            <h2 class="section-title">Thông tin cơ bản</h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Mã đơn hàng</div>
                    <div class="info-value">{{ $request->order_number }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Trạng thái</div>
                    <div class="info-value">
                        <span class="status status-{{ $request->status === 'Chờ xử lý' ? 'pending' : ($request->status === 'Đã chấp nhận' ? 'approved' : 'rejected') }}">
                            {{ $request->status === 'Chờ xử lý' ? '⏳ Chờ xử lý' : ($request->status === 'Đã chấp nhận' ? '✓ Đã chấp nhận' : '✗ Đã từ chối') }}
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Ngày tạo</div>
                    <div class="info-value">{{ $request->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Cập nhật lần cuối</div>
                    <div class="info-value">{{ $request->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="section-title">Sản phẩm cần đổi trả</h2>
            
            <div class="products-list">
                @foreach($request->products as $product)
                    <div class="product-item">
                        <p class="product-name">✓ {{ $product }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <h2 class="section-title">Lý do đổi trả</h2>
            
            <div class="reason-box">
                <div class="reason-label">Mô tả chi tiết</div>
                <div class="reason-text">{{ $request->reason }}</div>
            </div>

            @if($request->rejection_reason)
                <div class="reason-box" style="margin-top: 16px; border-left-color: #b91c1c;">
                    <div class="reason-label" style="color: #b91c1c;">Lý do từ chối</div>
                    <div class="reason-text">{{ $request->rejection_reason }}</div>
                </div>
            @endif
        </div>

        <div class="card">
            <h2 class="section-title">Thao tác</h2>
            
            <div class="actions">
                <a href="{{ route('warranty.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
                @if($request->status === 'Chờ xử lý')
                    <a href="{{ route('warranty.request') }}" class="btn btn-primary">Gửi yêu cầu mới</a>
                @endif
            </div>

            <div class="timeline">
                <h3 style="margin: 0 0 16px; font-size: 16px;">Lịch sử</h3>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="timeline-date">{{ $request->created_at->format('d/m/Y H:i') }}</div>
                        <div class="timeline-text">Yêu cầu được tạo - Trạng thái: <strong>{{ $request->status }}</strong></div>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="timeline-date">{{ $request->updated_at->format('d/m/Y H:i') }}</div>
                        <div class="timeline-text">Cập nhật lần cuối</div>
                    </div>
                </div>
                @if($request->processed_at)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <div class="timeline-date">{{ $request->processed_at->format('d/m/Y H:i') }}</div>
                            <div class="timeline-text">Yêu cầu đã được xử lý với trạng thái <strong>{{ $request->status }}</strong></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
