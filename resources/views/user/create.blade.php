@extends('layouts.app')
@section('title', 'Entri Data User')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-4">
    <div class="page-header text-white">
      <h4 class="page-title text-white"><i class="fas fa-user mr-2"></i> Manajemen User</h4>
      <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('user.index') }}" class="text-white">User</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Entri</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Entri Data User</div>
    </div>
    <form action="{{ route('user.store') }}" method="POST" class="needs-validation" novalidate>
      @csrf
      <div class="card-body">
        <div class="form-group col-lg-5">
          <label>Nama User <span class="text-danger">*</span></label>
          <input type="text" name="nama_user" class="form-control" autocomplete="off" value="{{ old('nama_user') }}" required>
          <div class="invalid-feedback">Nama user tidak boleh kosong.</div>
        </div>

        <div class="form-group col-lg-5">
          <label>Username <span class="text-danger">*</span></label>
          <input type="text" name="username" class="form-control" autocomplete="off" value="{{ old('username') }}" required>
          <div class="invalid-feedback">Username tidak boleh kosong.</div>
        </div>

        <div class="form-group col-lg-5">
          <label>Email <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control" autocomplete="off" value="{{ old('email') }}" required>
          <div class="invalid-feedback">Email tidak boleh kosong atau tidak valid.</div>
        </div>

        <div class="form-group col-lg-5">
          <label>Password <span class="text-danger">*</span></label>
          <input type="password" name="password" class="form-control" autocomplete="off" required>
          <div class="invalid-feedback">Password tidak boleh kosong.</div>
        </div>

        <div class="form-group col-lg-5">
          <label>Hak Akses <span class="text-danger">*</span></label>
          <select name="hak_akses" class="form-control chosen-select" autocomplete="off" required>
            <option selected disabled value="">-- Pilih --</option>
            <option value="Administrator" {{ old('hak_akses') == 'Administrator' ? 'selected' : '' }}>Administrator</option>
            <option value="Admin Gudang" {{ old('hak_akses') == 'Admin Gudang' ? 'selected' : '' }}>Admin Gudang</option>
            <option value="Kepala Gudang" {{ old('hak_akses') == 'Kepala Gudang' ? 'selected' : '' }}>Kepala Gudang</option>
            <option value="Teknisi" {{ old('hak_akses') == 'Teknisi' ? 'selected' : '' }}>Teknisi</option>
          </select>
          <div class="invalid-feedback">Hak akses tidak boleh kosong.</div>
        </div>
      </div>
      <div class="card-action">
        <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
        <a href="{{ route('user.index') }}" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
