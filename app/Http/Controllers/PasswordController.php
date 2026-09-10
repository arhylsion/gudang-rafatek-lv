<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /**
     * Port 1:1 _legacy/modules/password/form_ubah.php
     */
    public function edit()
    {
        return view('password.edit');
    }

    /**
     * Port 1:1 _legacy/modules/password/proses_ubah.php
     * Verifikasi password lama, baru==konfirmasi, min 6, Hash::make cost 12.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:6',
            'konfirmasi_password' => 'required|string|min:6',
        ], [
            'password_lama.required' => 'Password lama tidak boleh kosong.',
            'password_baru.required' => 'Password baru tidak boleh kosong.',
            'password_baru.min' => 'Password baru minimal 6 karakter.',
            'konfirmasi_password.required' => 'Konfirmasi password baru tidak boleh kosong.',
        ]);

        $user = Auth::user()->fresh();

        if (! Hash::check($validated['password_lama'], $user->password)) {
            return back()->with('error', 'Password Lama yang Anda masukan salah.');
        }

        if ($validated['password_baru'] !== $validated['konfirmasi_password']) {
            return back()->with('error', 'Password Baru dan Konfirmasi Password Baru tidak cocok.');
        }

        $user->password = Hash::make($validated['password_baru'], ['rounds' => 12]);
        $user->save();

        return redirect()->route('password.edit')->with('success', 'Password berhasil diubah.');
    }
}
