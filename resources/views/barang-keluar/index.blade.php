@extends('layouts.app')
@section('title', 'Data Barang Keluar')

@php
  $hariIndo = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
  ];
  $role = Auth::user()->hak_akses ?? '';
  $isTeknisi = $role === 'Teknisi';
  $canApprove = in_array($role, ['Administrator', 'Admin Gudang']);
@endphp

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-45">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-sign-out-alt mr-2"></i> Barang Keluar</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="{{ route('barang-keluar.index') }}" class="text-white">Barang Keluar</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Data</a></li>
        </ul>
      </div>
      @if(!$isTeknisi)
      <div class="ml-md-auto py-2 py-md-0">
        <a href="{{ route('barang-keluar.create', ['type' => 'keluar']) }}" class="btn btn-success btn-round mr-2">
          <span class="btn-label"><i class="fa fa-sign-out-alt mr-2"></i></span> Barang Keluar
        </a>
        <a href="{{ route('barang-keluar.create', ['type' => 'pinjam']) }}" class="btn btn-warning btn-round mr-2">
          <span class="btn-label"><i class="fa fa-handshake mr-2"></i></span> Peminjaman
        </a>
        <a href="{{ route('barang-keluar.pengembalian.form') }}" class="btn btn-info btn-round">
          <span class="btn-label"><i class="fa fa-undo mr-2"></i></span> Pengembalian
        </a>
      </div>
      @endif
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="d-flex align-items-center flex-wrap">
        <div class="card-title">Data Barang Keluar</div>
        <form method="GET" action="{{ route('barang-keluar.index') }}" class="ml-auto d-flex">
          <select name="jenis" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
            <option value="">-- Semua Jenis --</option>
            <option value="Keluar" {{ $fJenis === 'Keluar' ? 'selected' : '' }}>Keluar</option>
            <option value="Pinjam" {{ $fJenis === 'Pinjam' ? 'selected' : '' }}>Pinjam</option>
          </select>
          <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
            <option value="">-- Semua Status --</option>
            <option value="Menunggu Persetujuan" {{ $fStatus === 'Menunggu Persetujuan' ? 'selected' : '' }}>Menunggu Persetujuan</option>
            <option value="Disetujui" {{ $fStatus === 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
            <option value="Ditolak" {{ $fStatus === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
          </select>
          @if($fStatus || $fJenis)
            <a href="{{ route('barang-keluar.index') }}" class="btn btn-default btn-sm btn-round">Reset</a>
          @endif
        </form>
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
              <th class="text-center">Serial Number</th>
              <th class="text-center">Barang</th>
              <th class="text-center">Foto</th>
              <th class="text-center">Jumlah Keluar</th>
              <th class="text-center">Satuan</th>
              <th class="text-center">Jenis</th>
              <th class="text-center">Dibuat Oleh</th>
              <th class="text-center">Status</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($keluar as $i => $d)
              @php
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
                <td class="text-center">{{ $d->jenis }}</td>
                <td class="text-center">
                  @if($d->creator)
                    <span class="badge badge-info">{{ $d->creator->nama_user }}</span>
                  @else
                    <span class="badge badge-secondary">Tidak diketahui</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($d->status === 'Menunggu Persetujuan')
                    <span class="badge badge-warning">Menunggu Persetujuan</span>
                  @elseif($d->status === 'Disetujui' || $d->status === 'Barang Keluar')
                    <span class="badge badge-success">{{ $d->status }}</span>
                  @else
                    <span class="badge badge-danger">{{ $d->status }}</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($isTeknisi)
                    <a href="{{ route('barang-keluar.show', $d->id_transaksi) }}" class="btn btn-icon btn-round btn-secondary btn-sm" data-toggle="tooltip" title="Detail Transaksi">
                      <i class="fas fa-info-circle fa-sm"></i>
                    </a>
                  @else
                    @if($d->status === 'Menunggu Persetujuan' && $canApprove)
                      <a href="{{ route('barang-keluar.status', ['id' => $d->id_transaksi, 'status' => 'Disetujui']) }}"
                         class="btn btn-icon btn-round btn-success btn-sm" data-toggle="tooltip" title="Setujui"
                         onclick="return confirm('Setujui barang keluar {{ $d->id_transaksi }}?')">
                        <i class="fas fa-check fa-sm"></i>
                      </a>
                      <a href="{{ route('barang-keluar.status', ['id' => $d->id_transaksi, 'status' => 'Ditolak']) }}"
                         class="btn btn-icon btn-round btn-warning btn-sm" data-toggle="tooltip" title="Tolak"
                         onclick="return confirm('Tolak barang keluar {{ $d->id_transaksi }}?')">
                        <i class="fas fa-times fa-sm"></i>
                      </a>
                    @endif
                    <a href="{{ route('barang-keluar.show', $d->id_transaksi) }}" class="btn btn-icon btn-round btn-secondary btn-sm" data-toggle="tooltip" title="Detail Transaksi">
                      <i class="fas fa-info-circle fa-sm"></i>
                    </a>
                    <form action="{{ route('barang-keluar.destroy', $d->id_transaksi) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Anda yakin ingin menghapus data barang keluar {{ $d->id_transaksi }}?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-icon btn-round btn-danger btn-sm" data-toggle="tooltip" title="Hapus">
                        <i class="fas fa-trash fa-sm"></i>
                      </button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="12" class="text-center">Tidak ada data yang ditemukan.</td></tr>
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
