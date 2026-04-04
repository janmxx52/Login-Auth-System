<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    @vite('resources/css/register.css')
</head>
<body>
    <div class="shell">
        <div class="card">
            <h1 class="headline">Tạo tài khoản mới</h1>
            <p class="subline">Thiết lập thông tin cơ bản để truy cập hệ thống quản trị. Mật khẩu tối thiểu 6 ký tự, nên bật xác thực hai bước sau khi đăng nhập.</p>
            <div class="pill">
                <span class="accent-dot"></span>
                <span>Hãy dùng email công ty. Nếu đã có tài khoản, bạn có thể <a href="/login" style="color: var(--accent); text-decoration: none;">đăng nhập</a> ngay.</span>
            </div>
        </div>

        <div class="card">
            @if ($errors->any())
                <div class="errors">
                    @foreach ($errors->all() as $error)
                        • {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            @if (session('success'))
                <div class="success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="/register" novalidate>
                @csrf
                <div class="grid">
                    <div class="field">
                        <label for="name">Họ và tên</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Nguyễn Văn A" required>
                    </div>
                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="name@company.com" required>
                    </div>
                </div>

                <div class="grid">
                    <div class="field">
                        <label for="password">Mật khẩu</label>
                        <input id="password" type="password" name="password" placeholder="••••••••" minlength="6" required>
                    </div>
                    <div class="field">
                        <label for="password_confirmation">Nhập lại mật khẩu</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="••••••••" minlength="6" required>
                    </div>
                </div>

                <button class="submit" type="submit">Đăng ký</button>
                <div class="hint">Bấm đăng ký đồng nghĩa bạn đồng ý với chính sách bảo mật nội bộ.</div>
            </form>
        </div>
    </div>
</body>
</html>
