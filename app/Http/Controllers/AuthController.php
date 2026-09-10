<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    // 1:1 dengan proses_login.php native: login pakai username ATAU email + password_verify
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username atau email tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
        ]);

        $login = $request->input('username');
        $user = User::where('username', $login)->orWhere('email', $login)->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            Auth::login($user, false);
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Selamat datang, '.$user->nama_user.'!');
        }

        return redirect()->route('login', ['pesan' => 1])->with('error', 'Username/Email atau Password salah. Cek kembali Username/Email dan Password Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login', ['pesan' => 3])->with('success', 'Anda telah berhasil logout.');
    }
}
