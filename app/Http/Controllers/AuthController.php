<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect('/login');
        }

        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Đăng ký thành công');
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $key = 'login-' . $request->email . '-' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->withErrors([
                'email' => 'Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau.',
            ]);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            RateLimiter::clear($key);

            $user = Auth::user();
            $target = $user->role === 'admin' ? '/admin' : '/dashboard';

            return redirect()->intended($target);
        }

        RateLimiter::hit($key, 60);

        return back()->withErrors([
            'email' => 'Sai email hoặc mật khẩu',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
