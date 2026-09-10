@extends('layouts.app')
@section('title', 'Laporan Barang Keluar')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-4">
    <div class="page-header text-white">
      <h4 class="page-title text-white"><i class="fas fa-file-export mr-2"></i> Laporan Barang Keluar</h4>
      <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Laporan</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Barang Keluar</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Filter Data Barang Keluar</div>
    </div>
    <div class="card-body">
      <form action="{{ route('laporan.keluar') }}" method="GET" class="needs-validation" novalidate>
        <div class="row">
          <div class="col-lg-3">
            <div class="form-group">
              <label>Tanggal Awal <span class="text-danger">*</span></label>
              <input type="text" name="tanggal_awal" class="form-control date-picker" autocomplete="off" required value="{{ $fAwal ?? '' }}">
              <div class="invalid-feedback">Tanggal awal tidak boleh kosong.</div>
            </div>
          </div>

          <div class="col-lg-3">
            <div class="form-group">
              <label>Tanggal Akhir <span class="text-danger">*</span></label>
              <input type="text" name="tanggal_akhir" class="form-control date-picker" autocomplete="off" required value="{{ $fAkhir ?? '' }}">
              <div class="invalid-feedback">Tanggal akhir tidak boleh kosong.</div>
            </div>
          </div>

          <div class="col-lg-2">
            <div class="form-group">
              <label>Status</label>
              <select name="status" class="form-control chosen-select">
                <option value="">-- Semua --</option>
                <option value="Menunggu Persetujuan" {{ ($fStatus ?? '') == 'Menunggu Persetujuan' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="Disetujui" {{ ($fStatus ?? '') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="Ditolak" {{ ($fStatus ?? '') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
              </select>
            </div>
          </div>

          <div class="col-lg-2">
            <div class="form-group">
              <label>Jenis</label>
              <select name="jenis" class="form-control chosen-select">
                <option value="">-- Semua --</option>
                <option value="Keluar" {{ ($fJenis ?? '') == 'Keluar' ? 'selected' : '' }}>Keluar</option>
                <option value="Pinjam" {{ ($fJenis ?? '') == 'Pinjam' ? 'selected' : '' }}>Pinjam</option>
              </select>
            </div>
          </div>

          <div class="col-lg-2 pr-0">
            <div class="form-group pt-3">
              <input type="submit" name="tampil" value="Tampilkan" class="btn btn-secondary btn-round btn-block mt-4">
            </div>
          </div>

          @if($fAwal && $fAkhir)
          @php $cetakParams = array_filter(['tanggal_awal' => $fAwal, 'tanggal_akhir' => $fAkhir, 'status' => $fStatus ?? null, 'jenis' => $fJenis ?? null]); @endphp
          <div class="col-lg-2 pr-0">
            <div class="form-group pt-3">
              <a href="{{ route('laporan.keluar-cetak', $cetakParams) }}" target="_blank" class="btn btn-warning btn-round btn-block mt-4">
                <span class="btn-label"><i class="fa fa-print mr-2"></i></span> Cetak
              </a>
            </div>
          </div>

          <div class="col-lg-2 pl-0">
            <div class="form-group pt-3">
              <a href="{{ route('laporan.keluar-export', $cetakParams) }}" target="_blank" class="btn btn-success btn-round btn-block mt-4">
                <span class="btn-label"><i class="fa fa-file-excel mr-2"></i></span> Export
              </a>
            </div>
          </div>
          @endif
        </div>
      </form>
    </div>
  </div>

  @if($fAwal && $fAkhir)
  <div class="card">
    <div class="card-header">
      <div class="card-title">
        <i class="fas fa-file-alt mr-2"></i> Laporan Data Barang Keluar Tanggal <strong>{{ $fAwal }}</strong> s.d. <strong>{{ $fAkhir }}</strong>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center">No.</th>
              <th class="text-center">ID Transaksi</th>
              <th class="text-center">Tanggal</th>
              <th class="text-center">Barang</th>
              <th class="text-center">Foto</th>
              <th class="text-center">Jumlah Keluar</th>
              <th class="text-center">Satuan</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $i => $d)
              <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="text-center">{{ $d->id_transaksi }}</td>
                <td class="text-center">{{ hari_indo($d->tanggal) . ', ' . date('d-m-Y', strtotime($d->tanggal)) }}</td>
                <td>{{ $d->barangRel->nama_barang ?? $d->barang }}</td>
                <td class="text-center">
                  @if(!empty($d->barangRel->foto) && file_exists(public_path('images/'.$d->barangRel->foto)))
                    <img src="{{ asset('images/'.$d->barangRel->foto) }}" width="60" height="60" style="object-fit: contain;">
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td class="text-right">{{ number_format($d->jumlah, 0, '', '.') }}</td>
                <td>{{ $d->barangRel->satuanRel->nama_satuan ?? '-' }}</td>
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
