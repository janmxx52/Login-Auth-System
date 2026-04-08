<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id')->get();
        $roles = config('roles');

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = config('roles');
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $roles = config('roles');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in($roles)],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Tạo user thành công');
    }

    public function edit(User $user)
    {
        $roles = config('roles');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $roles = config('roles');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role' => ['required', Rule::in($roles)],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'Cập nhật user thành công');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')->withErrors([
                'delete' => 'Không thể tự xóa tài khoản admin đang đăng nhập.',
            ]);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'Đã xóa user');
    }
}
