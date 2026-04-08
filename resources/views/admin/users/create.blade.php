<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo user</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin:0; background:#f8fafc; color:#0f172a; }
        .wrap { max-width: 620px; margin: 30px auto; padding: 0 16px; }
        .card { background:#fff; border-radius:14px; padding:20px; box-shadow:0 12px 30px rgba(15,23,42,0.08); border:1px solid #e5e7eb; }
        label { display:block; margin-bottom:6px; font-weight:600; }
        input, select { width:100%; padding:12px 10px; border:1px solid #cbd5e1; border-radius:12px; font-size:15px; }
        input:focus, select:focus { outline:2px solid #0ea5e9; }
        .field { margin-bottom:14px; }
        .btn { border:none; cursor:pointer; padding:12px 14px; border-radius:12px; font-weight:700; color:#fff; background:#0f766e; box-shadow:0 10px 25px rgba(15,118,110,0.2); }
        .link { text-decoration:none; color:#2563eb; font-weight:700; }
        .error { margin-bottom: 10px; padding:10px 12px; border-radius:12px; background:#fef2f2; color:#b91c1c; border:1px solid #fecdd3; }
    </style>
</head>
<body>
    <div class="wrap">
        <a class="link" href="{{ route('admin.users.index') }}">← Danh sách</a>
        <div class="card">
            <h2 style="margin-top:0;">Tạo user mới</h2>

            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        • {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="field">
                    <label for="name">Tên</label>
                    <input id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="field">
                    <label for="password">Mật khẩu (≥6 ký tự)</label>
                    <input id="password" type="password" name="password" required>
                </div>
                <div class="field">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(old('role') === $role)>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn" type="submit">Tạo user</button>
            </form>
        </div>
    </div>
</body>
</html>
