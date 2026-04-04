<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    @vite('resources/css/dashboard.css')
</head>
<body>
    <div class="topbar">
        <div class="title">
            <div class="pill">
                <span class="accent-dot"></span>
            </div>
            <h1 class="headline">Xin chào, {{ $user->name }} 👋</h1>
            <p class="subline">Bạn đang ở bảng điều khiển. Quản lý tài khoản và kiểm tra trạng thái truy cập tại đây.</p>
        </div>
        <form method="POST" action="/logout">
            @csrf
            <button class="logout-btn" type="submit">Đăng xuất</button>
        </form>
    </div>

    <div class="grid" style="margin-bottom: 16px;">
        <div class="card">
            <h3>Tên hiển thị</h3>
            <p class="value">{{ $user->name }}</p>
            <p class="muted">Thông tin lấy từ hồ sơ người dùng.</p>
        </div>
        <div class="card">
            <h3>Email đăng nhập</h3>
            <p class="value">{{ $user->email }}</p>
            <p class="muted">Địa chỉ email dùng cho xác thực.</p>
        </div>
        <div class="card">
            <h3>Ngày tạo tài khoản</h3>
            <p class="value">
                {{ optional($user->created_at)->timezone(config('app.timezone'))->format('d/m/Y') }}
            </p>
            <p class="muted">Theo múi giờ hệ thống.</p>
        </div>
    </div>

    <div class="row">
        <div class="card">
            <h3>Hoạt động gần đây</h3>
            <div class="list">
                <div class="list-item">
                    <span>Đăng nhập thành công</span>
                    <span class="muted">Phiên hiện tại</span>
                </div>
                <div class="list-item">
                    <span>Cập nhật hồ sơ</span>
                    <span class="muted">Chưa có log</span>
                </div>
                <div class="list-item">
                    <span>Bảo mật</span>
                    <span class="muted">Khuyến nghị bật 2FA</span>
                </div>
            </div>
        </div>
        <div class="card">
            <h3>Hướng dẫn nhanh</h3>
            <div class="list">
                <div class="list-item">
                    <span>Quản lý phiên</span>
                    <span class="muted">Đăng xuất khi dùng máy lạ</span>
                </div>
                <div class="list-item">
                    <span>Đổi mật khẩu định kỳ</span>
                    <span class="muted">Mỗi 90 ngày</span>
                </div>
                <div class="list-item">
                    <span>Liên hệ hỗ trợ</span>
                    <span class="muted">it-support@company.local</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
