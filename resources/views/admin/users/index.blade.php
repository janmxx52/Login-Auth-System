<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý user</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .wrap { max-width: 1100px; margin: 30px auto; padding: 0 16px; }
        .top { display:flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .btn { display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border-radius:12px; border:none; cursor:pointer; font-weight:700; text-decoration:none; }
        .btn-primary { background:#0f766e; color:#fff; box-shadow:0 10px 25px rgba(15,118,110,0.2); }
        .btn-secondary { background:#2563eb; color:#fff; }
        .card { background:#fff; border-radius:14px; padding:16px; box-shadow:0 12px 30px rgba(15,23,42,0.08); border:1px solid #e5e7eb; }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:10px 8px; border-bottom:1px solid #e5e7eb; text-align:left; }
        th { font-weight:700; color:#334155; }
        .badge { padding:6px 10px; border-radius:999px; font-weight:700; font-size:12px; display:inline-block; }
        .badge-admin { background:#ecfeff; color:#0f766e; }
        .badge-user { background:#f1f5f9; color:#475569; }
        .actions { display:flex; gap:8px; }
        form { margin:0; }
        .status { margin-bottom: 10px; padding:10px 12px; border-radius:12px; background:#ecfdf3; color:#166534; border:1px solid #bbf7d0; }
        .error { margin-bottom: 10px; padding:10px 12px; border-radius:12px; background:#fef2f2; color:#b91c1c; border:1px solid #fecdd3; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="top">
            <h2 style="margin:0;">Danh sách user</h2>
            <div style="display:flex; gap:10px;">
                <a class="btn btn-secondary" href="/admin">← Admin</a>
                <a class="btn btn-primary" href="{{ route('admin.users.create') }}">+ Tạo user</a>
            </div>
        </div>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    • {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th style="width:160px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge {{ $user->role === 'admin' ? 'badge-admin' : 'badge-user' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="actions">
                                <a class="btn btn-secondary" href="{{ route('admin.users.edit', $user) }}">Sửa</a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Xóa user này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn" style="background:#f43f5e;color:#fff;">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
