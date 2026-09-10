@extends('layouts.app')
@section('title', 'Ubah Satuan')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-4">
    <div class="page-header text-white">
      <h4 class="page-title text-white"><i class="fas fa-clone mr-2"></i> Satuan</h4>
      <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('satuan.index') }}" class="text-white">Satuan</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Ubah</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Ubah Data Satuan</div>
    </div>
    <form action="{{ route('satuan.update', $satuan->id_satuan) }}" method="POST" class="needs-validation" novalidate>
      @csrf
      @method('PUT')
      <div class="card-body">
        <div class="form-group">
          <label>Satuan <span class="text-danger">*</span></label>
          <input type="text" name="nama_satuan" class="form-control col-lg-5" autocomplete="off" value="{{ old('nama_satuan', $satuan->nama_satuan) }}" required>
          <div class="invalid-feedback">Satuan tidak boleh kosong.</div>
        </div>
      </div>
      <div class="card-action">
        <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
        <a href="{{ route('satuan.index') }}" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
