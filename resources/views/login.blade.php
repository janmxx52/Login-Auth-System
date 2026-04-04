<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    @vite('resources/css/login.css')
</head>
<body>
    <div class="shell">
        <div class="card">
            <h1 class="headline">Chào mừng trở lại</h1>
            <p class="subline">Đăng nhập để tiếp tục quản trị. Bảo mật hai lớp đang bật, hãy giữ thông tin đăng nhập an toàn.</p>
            <div class="pill">
                <span class="accent-dot"></span>
                <span>Phiên đăng nhập sẽ tự động khóa sau 20 phút không hoạt động.</span>
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

            <form method="POST" action="/login">
                @csrf
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" placeholder="name@example.com" required autofocus>
                </div>

                <div class="field">
                    <label for="password">Mật khẩu</label>
                    <input id="password" type="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="remember">
                    <input id="remember" type="checkbox" name="remember">
                    <label for="remember">Ghi nhớ đăng nhập trên thiết bị này</label>
                </div>

                <button class="submit" type="submit">Đăng nhập</button>
                <div class="hint">Bạn chưa có tài khoản? <a href="/register" style="color: var(--accent); text-decoration: none;">Đăng ký ngay</a>.</div>
            </form>
        </div>
    </div>
</body>
</html>
