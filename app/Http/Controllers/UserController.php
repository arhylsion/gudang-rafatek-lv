<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Port 1:1 _legacy/modules/user/tampil_data.php
     */
    public function index()
    {
        $users = User::orderByDesc('id_user')->get();

        return view('user.index', compact('users'));
    }

    /**
     * Port 1:1 _legacy/modules/user/form_entri.php
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Port 1:1 _legacy/modules/user/proses_entri.php
     * Cek duplikat username/email (legacy pesan=4/5), Hash::make cost 12.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_user' => 'required|string|max:30',
            'username' => 'required|string|max:30|unique:tbl_user,username',
            'email' => 'required|email|max:100|unique:tbl_user,email',
            'password' => 'required|string|min:6',
            'hak_akses' => ['required', Rule::in(['Administrator', 'Admin Gudang', 'Kepala Gudang', 'Teknisi'])],
        ], [
            'nama_user.required' => 'Nama user tidak boleh kosong.',
            'username.required' => 'Username tidak boleh kosong.',
            'username.unique' => 'Username ' . $request->input('username') . ' sudah ada. Silahkan ganti username yang Anda masukan.',
            'email.required' => 'Email tidak boleh kosong atau tidak valid.',
            'email.unique' => 'Email ' . $request->input('email') . ' sudah ada. Silahkan ganti email yang Anda masukan.',
            'password.required' => 'Password tidak boleh kosong.',
            'hak_akses.required' => 'Hak akses tidak boleh kosong.',
        ]);

        User::create([
            'nama_user' => trim($validated['nama_user']),
            'username' => trim($validated['username']),
            'email' => trim($validated['email']),
            'password' => Hash::make($validated['password'], ['rounds' => 12]),
            'hak_akses' => $validated['hak_akses'],
        ]);

        return redirect()->route('user.index')->with('success', 'Data user berhasil disimpan.');
    }

    /**
     * Port 1:1 _legacy/modules/user/form_ubah.php
     */
    public function edit(string $id)
    {
        $user = User::where('id_user', $id)->firstOrFail();

        return view('user.edit', compact('user'));
    }

    /**
     * Port 1:1 _legacy/modules/user/proses_ubah.php
     * Duplikat dicek kecuali diri sendiri; password opsional.
     */
    public function update(Request $request, string $id)
    {
        $user = User::where('id_user', $id)->firstOrFail();

        $validated = $request->validate([
            'nama_user' => 'required|string|max:30',
            'username' => 'required|string|max:30|unique:tbl_user,username,' . $user->id_user . ',id_user',
            'email' => 'required|email|max:100|unique:tbl_user,email,' . $user->id_user . ',id_user',
            'password' => 'nullable|string|min:6',
            'hak_akses' => ['required', Rule::in(['Administrator', 'Admin Gudang', 'Kepala Gudang', 'Teknisi'])],
        ], [
            'nama_user.required' => 'Nama user tidak boleh kosong.',
            'username.required' => 'Username tidak boleh kosong.',
            'username.unique' => 'Username ' . $request->input('username') . ' sudah ada. Silahkan ganti username yang Anda masukan.',
            'email.required' => 'Email tidak boleh kosong atau tidak valid.',
            'email.unique' => 'Email ' . $request->input('email') . ' sudah ada. Silahkan ganti email yang Anda masukan.',
            'hak_akses.required' => 'Hak akses tidak boleh kosong.',
        ]);

        $user->nama_user = trim($validated['nama_user']);
        $user->username = trim($validated['username']);
        $user->email = trim($validated['email']);
        $user->hak_akses = $validated['hak_akses'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password'], ['rounds' => 12]);
        }

        $user->save();

        return redirect()->route('user.index')->with('success', 'Data user berhasil diubah.');
    }

    /**
     * Port 1:1 _legacy/modules/user/proses_hapus.php
     */
    public function destroy(string $id)
    {
        $user = User::where('id_user', $id)->firstOrFail();
        $user->delete();

        return redirect()->route('user.index')->with('success', 'Data user berhasil dihapus.');
    }
}
