@extends('layouts.app')
@section('title', 'Entri Rak')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-4">
    <div class="page-header text-white">
      <h4 class="page-title text-white"><i class="fas fa-warehouse mr-2"></i> Data Rak</h4>
      <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('rak.index') }}" class="text-white">Data Rak</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Entri</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Entri Data Rak</div>
    </div>
    <form action="{{ route('rak.store') }}" method="POST" class="needs-validation" novalidate>
      @csrf
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Kode Rak <span class="text-danger">*</span></label>
              <input type="text" name="kode_rak" class="form-control" autocomplete="off" placeholder="Contoh: R001" value="{{ old('kode_rak') }}" required>
              <div class="invalid-feedback">Kode rak tidak boleh kosong.</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Nama Rak <span class="text-danger">*</span></label>
              <input type="text" name="nama_rak" class="form-control" autocomplete="off" placeholder="Contoh: Rak Utama 1" value="{{ old('nama_rak') }}" required>
              <div class="invalid-feedback">Nama rak tidak boleh kosong.</div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Lokasi</label>
              <input type="text" name="lokasi" class="form-control" autocomplete="off" placeholder="Contoh: Lantai 1 - Sektor A" value="{{ old('lokasi') }}">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Kapasitas</label>
              <input type="number" name="kapasitas" class="form-control" autocomplete="off" placeholder="0" min="0" value="{{ old('kapasitas') }}">
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>Keterangan</label>
          <textarea name="keterangan" class="form-control" rows="3" placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
        </div>
      </div>
      <div class="card-action">
        <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
        <a href="{{ route('rak.index') }}" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
