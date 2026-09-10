@extends('layouts.app')
@section('title', $isAdminEdit ? 'Edit Profil User' : 'Edit Profil')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-4">
    <div class="page-header text-white">
      <h4 class="page-title text-white">
        <i class="fas fa-user-edit mr-2"></i>
        {{ $isAdminEdit ? 'Edit Profil User' : 'Edit Profil' }}
      </h4>
      <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        @if($isAdminEdit)
        <li class="nav-item"><a href="{{ route('user.index') }}" class="text-white">User</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        @endif
        <li class="nav-item"><a>{{ $isAdminEdit ? 'Edit Profil User' : 'Edit Profil' }}</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">{{ $isAdminEdit ? 'Edit Profil User: ' . $user->nama_user : 'Edit Profil Saya' }}</div>
    </div>
    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
      @csrf
      @method('PUT')
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Foto Profil</label>
              <div class="text-center">
                @php
                  $fotoSrc = (!empty($user->foto_profil) && file_exists(public_path('assets/img/profiles/'.$user->foto_profil)))
                    ? asset('assets/img/profiles/'.$user->foto_profil)
                    : asset('assets/img/avatar-2.png');
                @endphp
                <img id="preview-foto" src="{{ $fotoSrc }}" alt="Foto Profil" class="img-thumbnail mb-3" style="width: 200px; height: 200px; object-fit: cover;">
                <div class="input-file-image">
                  <input type="file" name="foto_profil" id="foto_profil" class="form-control-file" accept="image/*">
                  <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maksimal 3MB.</small>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-8">
            <input type="hidden" name="id_user" value="{{ $user->id_user }}">

            <div class="form-group">
              <label>Nama Lengkap <span class="text-danger">*</span></label>
              <input type="text" name="nama_user" class="form-control" autocomplete="off" value="{{ old('nama_user', $user->nama_user) }}" required>
              <div class="invalid-feedback">Nama lengkap tidak boleh kosong.</div>
            </div>

            <div class="form-group">
              <label>Username <span class="text-danger">*</span></label>
              <input type="text" name="username" class="form-control" autocomplete="off" value="{{ old('username', $user->username) }}" required>
              <div class="invalid-feedback">Username tidak boleh kosong.</div>
            </div>

            <div class="form-group">
              <label>Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control" autocomplete="off" value="{{ old('email', $user->email) }}" required>
              <div class="invalid-feedback">Email tidak boleh kosong atau tidak valid.</div>
            </div>

            <div class="form-group">
              <label>Password Baru</label>
              <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password" autocomplete="off">
              <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
            </div>

            <div class="form-group">
              <label>Konfirmasi Password Baru</label>
              <input type="password" name="konfirmasi_password" class="form-control" placeholder="Konfirmasi password baru" autocomplete="off">
              <small class="form-text text-muted">Masukkan kembali password baru untuk konfirmasi.</small>
            </div>

            <div class="form-group">
              <label>Hak Akses @if($isAdminEdit)<span class="text-danger">*</span>@endif</label>
              @if($isAdminEdit)
              <select name="hak_akses" class="form-control chosen-select" autocomplete="off" required>
                <option value="{{ $user->hak_akses }}" selected>{{ $user->hak_akses }}</option>
                <option disabled value="">-- Pilih --</option>
                <option value="Administrator">Administrator</option>
                <option value="Admin Gudang">Admin Gudang</option>
                <option value="Kepala Gudang">Kepala Gudang</option>
                <option value="Teknisi">Teknisi</option>
              </select>
              <div class="invalid-feedback">Hak akses tidak boleh kosong.</div>
              @else
              <input type="text" class="form-control" value="{{ $user->hak_akses }}" readonly>
              <small class="form-text text-muted">Hak akses tidak dapat diubah.</small>
              @endif
            </div>
          </div>
        </div>
      </div>

      <div class="card-action">
        <input type="submit" name="simpan" value="Simpan Perubahan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
        <a href="{{ $isAdminEdit ? route('user.index') : route('dashboard') }}" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('js')
<script>
document.getElementById('foto_profil').addEventListener('change', function(e) {
  var file = e.target.files[0];
  if (file) {
    if (file.size > 3 * 1024 * 1024) {
      alert('Ukuran file terlalu besar. Maksimal 3MB.');
      this.value = '';
      return;
    }
    var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
      alert('Format file tidak valid. Gunakan JPG, JPEG, atau PNG.');
      this.value = '';
      return;
    }
    var reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('preview-foto').src = e.target.result;
    }
    reader.readAsDataURL(file);
  }
});
</script>
@endpush
