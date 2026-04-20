<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu cầu đổi trả</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #eef6f0; color: #0f172a; }
        .wrap { max-width: 1000px; margin: 30px auto; padding: 0 16px; }
        .card { background: #fff; border-radius: 18px; padding: 24px; box-shadow: 0 18px 40px rgba(15,23,42,.08); border: 1px solid #d6e4d7; }
        .top { display:flex; flex-wrap:wrap; gap:16px; justify-content:space-between; align-items:center; margin-bottom:24px; }
        h1 { margin: 0; font-size: 28px; }
        p { margin: 8px 0 0; color: #51636b; }
        .banner { background: #fef9c3; border: 1px solid #fde047; padding: 14px 16px; border-radius: 14px; color: #854d0e; margin-bottom: 24px; }
        .field { margin-bottom: 18px; }
        label { display:block; margin-bottom: 8px; font-weight:700; }
        input[type=text], input[type=email], select, textarea { width:100%; border:1px solid #d6e4d7; border-radius:14px; padding:14px 16px; font-size:15px; color:#0f172a; background:#fcfcfd; }
        textarea { min-height:140px; resize:vertical; }
        .products-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(160px, 1fr)); gap:12px; margin:12px 0; }
        .product-card { position:relative; border-radius:14px; border:2px solid #d6e4d7; overflow:hidden; transition:all .2s ease; cursor:pointer; }
        .product-card:has(input:checked) { border-color:#198754; background:#ecfdf5; box-shadow:0 0 0 2px #d1fae5; }
        .product-card input { position:absolute; opacity:0; width:0; height:0; }
        .product-image { width:100%; height:140px; background:#f8fbf8; overflow:hidden; display:grid; place-items:center; }
        .product-image img { width:100%; height:100%; object-fit:cover; }
        .product-info { padding:10px; }
        .product-name { font-size:13px; font-weight:600; margin:0; line-height:1.3; color:#0f172a; }
        .product-price { font-size:12px; color:#51636b; margin:4px 0 0; }
        .details { display:grid; gap:12px; margin-bottom:16px; }
        .detail-pill { display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border-radius:999px; background:#ecfdf5; color:#0a6640; font-weight:700; font-size:14px; }
        .button { border:none; border-radius:14px; padding:14px 20px; font-size:16px; font-weight:700; color:#fff; background:#198754; cursor:pointer; }
        .button:hover { background:#0a6640; }
        .button:disabled { opacity:.65; cursor:not-allowed; }
        .errors, .success { margin-bottom: 18px; padding: 14px 16px; border-radius: 14px; }
        .errors { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; }
        .success { background:#ecfdf5; color:#166534; border:1px solid #bbf7d0; }
        .note { color:#64748b; font-size:14px; margin-top:12px; }
        .link { display:inline-block; color:#0a6640; text-decoration:none; font-weight:700; margin-bottom:24px; }
    </style>
</head>
<body>
    <div class="wrap">
        <a class="link" href="/dashboard">← Quay lại Dashboard</a>
        
        <div class="top">
            <div>
                <h1>Yêu cầu đổi trả</h1>
                <p>Chọn sản phẩm mỹ phẩm cần đổi trả. Yêu cầu sẽ được xử lý trong vòng 1-2 ngày làm việc.</p>
            </div>
        </div>

        <div class="banner">
            Thời gian đổi trả: 7 ngày kể từ ngày nhận hàng. Trường hợp quá hạn hoặc đơn hàng không hợp lệ sẽ không được xử lý.
        </div>

        <div class="card">
            @if ($errors->any())
                <div class="errors">
                    @foreach ($errors->all() as $error)
                        • {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="/warranty/request">
                @csrf
                <div class="field">
                    <label for="order_key">Chọn đơn hàng</label>
                    <select id="order_key" name="order_key" required onchange="location.href='/warranty/request?order_key=' + this.value">
                        @php $selectedOrderKey = old('order_key', request()->query('order_key', array_key_first($orders))); @endphp
                        @foreach ($orders as $key => $order)
                            <option value="{{ $key }}" @selected($selectedOrderKey === $key)>
                                {{ $order['order_number'] }} — {{ $order['status'] === 'delivered' ? 'Đã nhận' : ucfirst($order['status']) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="details">
                    <div class="detail-pill">📦 {{ $selectedOrder['order_number'] }}</div>
                    <div class="detail-pill">{{ $selectedOrder['status'] === 'delivered' ? '✓ Đã giao' : '✗ ' . ucfirst($selectedOrder['status']) }}</div>
                    @if ($selectedOrder['status'] === 'delivered')
                        <div class="detail-pill">📅 Thời hạn: {{ $selectedOrder['return_window_days'] }} ngày</div>
                        <div class="detail-pill">📍 Nhận: {{ $selectedOrder['received_at']->format('d/m/Y') }}</div>
                    @endif
                </div>

                <div class="field">
                    <label>Sản phẩm mỹ phẩm cần đổi trả</label>
                    <div class="products-grid">
                        @foreach ($selectedOrder['products'] as $productKey => $product)
                            <label class="product-card">
                                <input type="checkbox" name="products[]" value="{{ $product['name'] }}" @checked(is_array(old('products')) && in_array($product['name'], old('products')))>
                                <div class="product-image">
                                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" onerror="this.style.display='none'">
                                </div>
                                <div class="product-info">
                                    <p class="product-name">{{ $product['name'] }}</p>
                                    <p class="product-price">{{ $product['price'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="field">
                    <label for="reason">Lý do yêu cầu đổi trả</label>
                    <textarea id="reason" name="reason" placeholder="Mô tả chi tiết lý do (tối thiểu 10 ký tự)..." required @error('reason') autofocus @enderror>{{ old('reason') }}</textarea>
                </div>

                <button class="button" type="submit">Gửi yêu cầu</button>
                <p class="note">✓ Trạng thái: <strong>Chờ xử lý</strong> | Quản lý tài khoản sẽ liên hệ bạn trong thời gian sớm nhất.</p>
            </form>
        </div>
    </div>
</body>
</html>
