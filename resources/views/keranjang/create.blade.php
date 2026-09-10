@extends('layouts.app')
@section('title', 'Entri Keranjang')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-4">
    <div class="page-header text-white">
      <h4 class="page-title text-white"><i class="fas fa-shopping-basket mr-2"></i> Data Keranjang</h4>
      <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('keranjang.index') }}" class="text-white">Data Keranjang</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Entri</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Entri Data Keranjang</div>
    </div>
    <form action="{{ route('keranjang.store') }}" method="POST" class="needs-validation" novalidate>
      @csrf
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Kode Keranjang <span class="text-danger">*</span></label>
              <input type="text" name="kode_keranjang" class="form-control" autocomplete="off" placeholder="Contoh: K001" value="{{ old('kode_keranjang') }}" required>
              <div class="invalid-feedback">Kode keranjang tidak boleh kosong.</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Nama Keranjang <span class="text-danger">*</span></label>
              <input type="text" name="nama_keranjang" class="form-control" autocomplete="off" placeholder="Contoh: Keranjang A1" value="{{ old('nama_keranjang') }}" required>
              <div class="invalid-feedback">Nama keranjang tidak boleh kosong.</div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label>Rak</label>
              <select name="id_rak" class="form-control">
                <option value="">-- Pilih Rak --</option>
                @foreach($rakList as $r)
                  <option value="{{ $r->id_rak }}" {{ old('id_rak') == $r->id_rak ? 'selected' : '' }}>{{ $r->kode_rak }} - {{ $r->nama_rak }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Kapasitas</label>
              <input type="number" name="kapasitas" class="form-control" autocomplete="off" placeholder="0" min="0" value="{{ old('kapasitas') }}">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Kondisi</label>
              <select name="kondisi" class="form-control">
                @foreach(['Baik', 'Rusak', 'Maintenance'] as $kondisi)
                  <option value="{{ $kondisi }}" {{ old('kondisi', 'Baik') == $kondisi ? 'selected' : '' }}>{{ $kondisi }}</option>
                @endforeach
              </select>
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
        <a href="{{ route('keranjang.index') }}" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
