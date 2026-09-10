@extends('layouts.app')
@section('title', 'Ubah Password')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-4">
    <div class="page-header text-white">
      <h4 class="page-title text-white"><i class="fas fa-user-lock mr-2"></i> Password</h4>
      <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Password</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Ubah</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Ubah Password</div>
    </div>
    <form action="{{ route('password.update') }}" method="POST" class="needs-validation" novalidate>
      @csrf
      @method('PUT')
      <div class="card-body">
        <div class="form-group">
          <label>Password Lama <span class="text-danger">*</span></label>
          <input type="password" name="password_lama" class="form-control col-lg-5" autocomplete="off" required>
          <div class="invalid-feedback">Password lama tidak boleh kosong.</div>
        </div>

        <div class="form-group">
          <label>Password Baru <span class="text-danger">*</span></label>
          <input type="password" name="password_baru" class="form-control col-lg-5" autocomplete="off" required>
          <div class="invalid-feedback">Password baru tidak boleh kosong.</div>
        </div>

        <div class="form-group">
          <label>Konfirmasi Password Baru <span class="text-danger">*</span></label>
          <input type="password" name="konfirmasi_password" class="form-control col-lg-5" autocomplete="off" required>
          <div class="invalid-feedback">Konfirmasi password baru tidak boleh kosong.</div>
        </div>
      </div>
      <div class="card-action">
        <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
      </div>
    </form>
  </div>
</div>
@endsection
