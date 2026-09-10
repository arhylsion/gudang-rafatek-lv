@extends('layouts.app')
@section('title', 'Laporan Stok')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-4">
    <div class="page-header text-white">
      <h4 class="page-title text-white"><i class="fas fa-file-signature mr-2"></i> Laporan Stok</h4>
      <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Laporan</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Stok</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Filter Data Stok</div>
    </div>
    <div class="card-body">
      <form action="{{ route('laporan.stok') }}" method="GET" class="needs-validation" novalidate>
        <div class="row">
          <div class="col-lg-5">
            <div class="form-group">
              <label>Stok <span class="text-danger">*</span></label>
              <select name="stok" class="form-control chosen-select" required>
                @if($fStok)
                  <option value="{{ $fStok }}" selected>{{ $fStok }}</option>
                  <option disabled>-- Pilih --</option>
                @else
                  <option selected disabled value="">-- Pilih --</option>
                @endif
                <option value="Seluruh">Seluruh</option>
                <option value="Minimum">Minimum</option>
              </select>
              <div class="invalid-feedback">Stok tidak boleh kosong.</div>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="form-group pt-3">
              <input type="submit" name="tampil" value="Tampilkan" class="btn btn-secondary btn-round btn-block mt-4">
            </div>
          </div>
          @if($fStok)
          <div class="col-lg-2 pr-0">
            <div class="form-group pt-3">
              <a href="{{ route('laporan.stok-cetak', ['stok' => $fStok]) }}" target="_blank" class="btn btn-warning btn-round btn-block mt-4">
                <span class="btn-label"><i class="fa fa-print mr-2"></i></span> Cetak
              </a>
            </div>
          </div>
          <div class="col-lg-2 pl-0">
            <div class="form-group pt-3">
              <a href="{{ route('laporan.stok-export', ['stok' => $fStok]) }}" target="_blank" class="btn btn-success btn-round btn-block mt-4">
                <span class="btn-label"><i class="fa fa-file-excel mr-2"></i></span> Export
              </a>
            </div>
          </div>
          @endif
        </div>
      </form>
    </div>
  </div>

  @if($fStok)
  <div class="card">
    <div class="card-header">
      <div class="card-title">
        <i class="fas fa-file-alt mr-2"></i>
        {{ $fStok == 'Seluruh' ? 'Laporan Stok Seluruh Barang' : 'Laporan Stok Barang yang Mencapai Batas Minimum' }}
        <br>
        <small class="text-muted">{{ hari_indo(date('Y-m-d')) . ', ' . date('d-m-Y') }}</small>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center">No.</th>
              <th class="text-center">ID Barang</th>
              <th class="text-center">Tanggal</th>
              <th class="text-center">Nama Barang</th>
              <th class="text-center">Jenis Barang</th>
              <th class="text-center">Foto</th>
              <th class="text-center">Stok</th>
              <th class="text-center">Satuan</th>
            </tr>
          </thead>
          <tbody>
            @php $tanggalCetak = hari_indo(date('Y-m-d')) . ', ' . date('d-m-Y'); @endphp
            @foreach($rows as $i => $d)
              @php $isMinimum = $d->stok <= $d->stok_minimum; @endphp
              <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="text-center">{{ $d->id_barang }}</td>
                <td class="text-center">{{ $tanggalCetak }}</td>
                <td>{{ $d->nama_barang }}</td>
                <td>{{ $d->jenisRel->nama_jenis ?? '-' }}</td>
                <td class="text-center">
                  @if(!empty($d->foto) && file_exists(public_path('images/'.$d->foto)))
                    <img src="{{ asset('images/'.$d->foto) }}" alt="Foto Barang" width="60" height="60" style="object-fit:contain;">
                  @else
                    <span class="text-muted">Tidak ada</span>
                  @endif
                </td>
                <td class="text-right">
                  @if($isMinimum)
                    <span class="badge badge-warning">{{ (int) $d->stok }}</span>
                  @else
                    {{ (int) $d->stok }}
                  @endif
                </td>
                <td class="text-center">{{ $d->satuanRel->nama_satuan ?? '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection
