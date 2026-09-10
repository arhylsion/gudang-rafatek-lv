<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    /**
     * Port 1:1 _legacy/modules/profil/form_edit_profil.php
     * Admin boleh ?id= untuk edit user lain, selain itu edit diri sendiri.
     */
    public function edit(Request $request)
    {
        $auth = Auth::user();

        if ($request->filled('id') && $auth->hak_akses === 'Administrator') {
            $user = User::where('id_user', $request->query('id'))->firstOrFail();
            $isAdminEdit = ((int) $user->id_user !== (int) $auth->id_user);
        } else {
            $user = User::where('id_user', $auth->id_user)->firstOrFail();
            $isAdminEdit = false;
        }

        return view('profil.edit', compact('user', 'isAdminEdit'));
    }

    /**
     * Port 1:1 _legacy/modules/profil/proses_edit_profil.php
     * 4 cabang: dengan/tanpa foto x dengan/tanpa password.
     * Foto -> public/assets/img/profiles/profile_{id}_{time}.ext (jpg/jpeg/png, max 3MB).
     * Sync session Auth bila edit diri sendiri.
     */
    public function update(Request $request)
    {
        $auth = Auth::user();
        $idUser = $request->input('id_user', $auth->id_user);

        $isAdminEdit = $auth->hak_akses === 'Administrator' && (int) $idUser !== (int) $auth->id_user;

        $user = User::where('id_user', $idUser)->firstOrFail();

        $validated = $request->validate([
            'id_user' => 'required|integer|exists:tbl_user,id_user',
            'nama_user' => 'required|string|max:30',
            'username' => 'required|string|max:30|unique:tbl_user,username,' . $user->id_user . ',id_user',
            'email' => 'required|email|max:100|unique:tbl_user,email,' . $user->id_user . ',id_user',
            'password' => 'nullable|string|min:6',
            'konfirmasi_password' => 'nullable|string|same:password',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
            'hak_akses' => ($isAdminEdit ? 'required' : 'nullable') . '|in:Administrator,Admin Gudang,Kepala Gudang,Teknisi',
        ], [
            'username.unique' => 'Username sudah digunakan. Silakan gunakan username lain.',
            'email.unique' => 'Email sudah digunakan. Silakan gunakan email lain.',
            'konfirmasi_password.same' => 'Password baru dan konfirmasi password tidak cocok.',
            'foto_profil.image' => 'File yang dipilih bukan gambar.',
            'foto_profil.max' => 'Ukuran file foto terlalu besar. Maksimal 3MB.',
            'foto_profil.mimes' => 'Format file tidak valid. Gunakan JPG, JPEG, atau PNG.',
        ]);

        // Password diisi tapi konfirmasi tidak cocok (legacy alert=4)
        if (! empty($validated['password']) && ($validated['password'] !== $request->input('konfirmasi_password'))) {
            return back()->withInput()->with('error', 'Password baru dan konfirmasi password tidak cocok.');
        }

        // Proses upload foto profil
        $namaFoto = $user->foto_profil;
        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $ext = strtolower($file->getClientOriginalExtension());
            $namaFoto = 'profile_' . $user->id_user . '_' . time() . '.' . $ext;
            $dest = public_path('assets/img/profiles');

            if (! is_dir($dest)) {
                mkdir($dest, 0777, true);
            }

            $file->move($dest, $namaFoto);

            // Hapus foto lama bila bukan avatar default
            $fotoLama = $user->foto_profil;
            if (! empty($fotoLama) && ! in_array($fotoLama, ['avatar-1.png', 'avatar-2.png'])) {
                $lamaPath = $dest . DIRECTORY_SEPARATOR . $fotoLama;
                if (is_file($lamaPath)) {
                    @unlink($lamaPath);
                }
            }
        }

        // 4 cabang update: dengan/tanpa foto x dengan/tanpa password
        $user->nama_user = trim($validated['nama_user']);
        $user->username = trim($validated['username']);
        $user->email = trim($validated['email']);
        $user->foto_profil = $namaFoto;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password'], ['rounds' => 12]);
        }

        if ($isAdminEdit && ! empty($validated['hak_akses'])) {
            $user->hak_akses = $validated['hak_akses'];
        }

        $user->save();

        // Sync session Auth bila edit diri sendiri (legacy update $_SESSION)
        if (! $isAdminEdit) {
            Auth::setUser($user->fresh());
        }

        $query = $isAdminEdit ? ['id' => $user->id_user] : [];

        return redirect()->route('profil.edit', $query)->with('success', 'Profil berhasil diperbarui.');
    }
}
