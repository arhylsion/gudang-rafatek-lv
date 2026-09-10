@extends('layouts.app')
@section('title', 'Stock Opname')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-45">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-clipboard-list mr-2"></i> Stock Opname</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="{{ route('opname.index') }}" class="text-white">Stock Opname</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Input</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Form Stock Opname</div>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('opname.store') }}" id="formOpname" class="needs-validation" novalidate>
        @csrf
        <div class="row">
          <div class="form-group col-md-4">
            <label>Kode & Nama Barang</label>
            <select name="id_barang" id="id_barang" class="form-control chosen-select" required>
              <option value="">-- Pilih Barang --</option>
              @foreach($barangList as $b)
                <option value="{{ $b->id_barang }}">{{ $b->id_barang }} - {{ $b->nama_barang }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group col-md-4">
            <label>Stok Sistem</label>
            <input type="text" id="stok_sistem" name="stok_sistem" class="form-control" readonly placeholder="--">
          </div>

          <div class="form-group col-md-4">
            <label>Stok Fisik</label>
            <input type="number" name="stok_fisik" id="stok_fisik" class="form-control" required min="0">
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-6">
            <label>Keterangan (Opsional)</label>
            <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Catatan tambahan (opsional)">
          </div>
          <div class="form-group col-md-6 d-flex align-items-end justify-content-end">
            <button type="submit" class="btn btn-primary btn-round">
              <i class="fas fa-save mr-2"></i> Simpan Opname
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div class="card-title">Rekapan & Riwayat Stock Opname</div>
      <form method="GET" action="{{ route('opname.index') }}">
        <div class="form-inline">
          <label class="mr-2">Tanggal Awal:</label>
          <input type="text" name="tanggal_awal" class="form-control date-picker mr-2" autocomplete="off" value="{{ $fAwal ?? '' }}">
          <label class="mr-2">Tanggal Akhir:</label>
          <input type="text" name="tanggal_akhir" class="form-control date-picker mr-2" autocomplete="off" value="{{ $fAkhir ?? '' }}">
          <button type="submit" name="tampil" value="1" class="btn btn-primary btn-sm">
            <i class="fas fa-filter"></i> Filter
          </button>
          @if($fAwal && $fAkhir)
            <a href="{{ route('opname.index') }}" class="btn btn-secondary btn-sm ml-2">
              <i class="fas fa-redo"></i> Reset
            </a>
          @endif
        </div>
      </form>
    </div>

    <div class="card-body">
      <div class="mb-3">
        @php
          $exportParams = ($fAwal && $fAkhir) ? ['tanggal_awal' => $fAwal, 'tanggal_akhir' => $fAkhir] : [];
        @endphp
        <a href="{{ route('opname.export-csv', $exportParams) }}" class="btn btn-success btn-sm">
          <i class="fas fa-file-excel mr-2"></i> Export
        </a>
        <a href="{{ route('opname.export-pdf', $exportParams) }}" class="btn btn-warning btn-sm" target="_blank">
          <i class="fas fa-print mr-2"></i> Cetak
        </a>
      </div>

      <div class="table-responsive">
        <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
          <thead>
            <tr class="text-center">
              <th>No</th>
              <th>Tanggal</th>
              <th>Serial Number</th>
              <th>Nama Barang</th>
              <th>Foto</th>
              <th>Stok Sistem</th>
              <th>Stok Fisik</th>
              <th>Selisih</th>
              <th>Status</th>
              <th>Keterangan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($riwayat as $i => $row)
              @php
                $selisih = $row->stok_fisik - $row->stok_sistem;
                $sn = $row->barang->serial_number ?? null;
              @endphp
              <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="text-center">{{ hari_indo($row->tanggal_opname) . ', ' . \Carbon\Carbon::parse($row->tanggal_opname)->format('d-m-Y') }}</td>
                <td class="text-center">
                  @if(!empty($sn))
                    <div>{{ $sn }}</div>
                    <svg class="barcode" jsbarcode-format="CODE128" jsbarcode-value="{{ $sn }}" jsbarcode-textmargin="2" jsbarcode-fontoptions="light" jsbarcode-width="1.2" jsbarcode-height="40"></svg>
                  @else
                    <div>ID Barang: {{ $row->id_barang }}</div>
                    <svg class="barcode" jsbarcode-format="CODE128" jsbarcode-value="{{ $row->id_barang }}" jsbarcode-textmargin="2" jsbarcode-fontoptions="bold" jsbarcode-width="1.2" jsbarcode-height="40"></svg>
                  @endif
                </td>
                <td>{{ $row->barang->nama_barang ?? '-' }}</td>
                <td class="text-center">
                  @if(!empty($row->barang->foto) && file_exists(public_path('images/'.$row->barang->foto)))
                    <img src="{{ asset('images/'.$row->barang->foto) }}" width="60" height="60" alt="Foto Barang">
                  @else
                    <span class="text-muted">Tidak ada</span>
                  @endif
                </td>
                <td class="text-right">{{ number_format($row->stok_sistem) }}</td>
                <td class="text-right">{{ number_format($row->stok_fisik) }}</td>
                <td class="text-center">
                  @if($selisih == 0)
                    <span class="badge badge-success">Sesuai</span>
                  @elseif($selisih > 0)
                    <span class="badge badge-warning">+{{ number_format($selisih) }}</span>
                  @else
                    <span class="badge badge-danger">{{ number_format($selisih) }}</span>
                  @endif
                </td>
                <td class="text-center">{{ $row->status ?? '-' }}</td>
                <td>{{ $row->keterangan }}</td>
                <td class="text-center">
                  <form action="{{ route('opname.destroy', $row->id_opname) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus data opname ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-icon btn-round btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Hapus">
                      <i class="fas fa-trash fa-sm"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" class="text-center">
                  <div class="py-3">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada data stock opname</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  if (typeof JsBarcode !== 'undefined') { JsBarcode(".barcode").init(); }
  var sel = document.getElementById('id_barang');
  if (sel) {
    sel.addEventListener('change', function() {
      var id = this.value;
      if (!id) { document.getElementById('stok_sistem').value = ''; return; }
      fetch("{{ route('opname.get-stok') }}?id_barang=" + encodeURIComponent(id))
        .then(function(res) { return res.json(); })
        .then(function(data) { document.getElementById('stok_sistem').value = (data && data.stok != null) ? data.stok : 0; })
        .catch(function(err) { console.error('Gagal ambil stok:', err); document.getElementById('stok_sistem').value = 'Error'; });
    });
  }
});
</script>
@endpush
