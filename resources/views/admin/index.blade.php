<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .wrap { max-width: 960px; margin: 30px auto; padding: 0 16px; }
        .card { background: #fff; border-radius: 14px; padding: 20px; box-shadow: 0 12px 30px rgba(15,23,42,0.08); border: 1px solid #e5e7eb; }
        .title { margin: 0 0 8px; font-size: 24px; font-weight: 700; }
        .muted { color: #6b7280; margin: 0 0 20px; }
        .actions a { display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 12px; background: #0f766e; color: #fff; text-decoration: none; font-weight: 600; box-shadow: 0 10px 25px rgba(15,118,110,0.2); }
        .pill { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 999px; background: #ecfeff; color: #0f766e; font-weight: 700; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <div class="pill">Admin mode</div>
            <h1 class="title">Xin chào, {{ $user->name }}</h1>
            <p class="muted">Bạn có toàn quyền quản lý người dùng và truy cập khu vực /admin.</p>
            <div class="actions">
                <a href="{{ route('admin.users.index') }}">Quản lý user →</a>
                <a href="/dashboard" style="margin-left:8px; background:#2563eb; box-shadow:0 10px 25px rgba(37,99,235,0.2);">Xem dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
