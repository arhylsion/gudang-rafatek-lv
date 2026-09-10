@extends('layouts.app')
@section('title', 'Data Barang Masuk')

@php
  $hariIndo = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
  ];
@endphp

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-45">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-sign-in-alt mr-2"></i> Barang Masuk</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="{{ route('barang-masuk.index') }}" class="text-white">Barang Masuk</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Data</a></li>
        </ul>
      </div>
      <div class="ml-md-auto py-2 py-md-0">
        <a href="{{ route('barang-masuk.create') }}" class="btn btn-secondary btn-round">
          <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data
        </a>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Data Barang Masuk</div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center">No.</th>
              <th class="text-center">ID Transaksi</th>
              <th class="text-center">Tanggal</th>
              <th class="text-center">Serial Number</th>
              <th class="text-center">Barang</th>
              <th class="text-center">Foto</th>
              <th class="text-center">Jumlah Masuk</th>
              <th class="text-center">Satuan</th>
              <th class="text-center">Lokasi Rak</th>
              <th class="text-center">Keranjang</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($masuk as $i => $d)
              @php
                // Prioritaskan serial transaksi, lalu serial barang, lalu id barang (legacy tampil_data.php)
                $sn = $d->serial_number ?: ($d->barangRel->serial_number ?? null) ?: $d->barang;
                $foto = $d->barangRel->foto ?? null;
              @endphp
              <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="text-center">{{ $d->id_transaksi }}</td>
                <td class="text-center">{{ ($hariIndo[date('l', strtotime($d->tanggal))] ?? '') . ', ' . date('d-m-Y', strtotime($d->tanggal)) }}</td>
                <td width="150" class="text-center">
                  <span style="font-weight:600;">{{ $sn }}</span>
                </td>
                <td>{{ $d->barangRel->nama_barang ?? '-' }}</td>
                <td class="text-center">
                  @if($foto && file_exists(public_path('images/'.$foto)))
                    <img src="{{ asset('images/'.$foto) }}" alt="Foto Barang" width="60" height="60" style="object-fit:cover;border-radius:8px;">
                  @else
                    <img src="{{ asset('images/no_image.png') }}" alt="Tidak ada foto" width="60" height="60" style="object-fit:cover;border-radius:8px;opacity:0.5;">
                  @endif
                </td>
                <td class="text-right">{{ number_format($d->jumlah, 0, '', '.') }}</td>
                <td class="text-center">{{ $d->barangRel->satuanRel->nama_satuan ?? '-' }}</td>
                <td class="text-center">
                  @if($d->rak)
                    {{ $d->rak->nama_rak }}
                    @if($d->rak->lokasi)
                      <br><small class="text-muted">{{ $d->rak->lokasi }}</small>
                    @endif
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($d->keranjang)
                    {{ $d->keranjang->nama_keranjang }}
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td class="text-center">
                  <form action="{{ route('barang-masuk.destroy', $d->id_transaksi) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Anda yakin ingin menghapus data barang masuk {{ $d->id_transaksi }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-icon btn-round btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Hapus">
                      <i class="fas fa-trash fa-sm"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="11" class="text-center">Tidak ada data yang ditemukan.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
  $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush
