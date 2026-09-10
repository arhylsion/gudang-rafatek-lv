@extends('layouts.app')
@section('title', 'Detail Transaksi '.$trx->id_transaksi)

@php
  $hariIndo = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
  ];
  $sn = $trx->serial_number ?: ($trx->barangRel->serial_number ?? null) ?: $trx->barang;
  $foto = $trx->barangRel->foto ?? null;
  $canApprove = in_array(Auth::user()->hak_akses ?? '', ['Administrator', 'Admin Gudang']);
@endphp

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-45">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-file-alt mr-2"></i> Detail Transaksi</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="{{ route('barang-keluar.index') }}" class="text-white">Barang Keluar</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Detail</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="d-flex align-items-center">
        <div class="card-title">Detail Transaksi #{{ $trx->id_transaksi }}</div>
        <div class="ml-auto">
          <a href="{{ route('barang-keluar.cetak', $trx->id_transaksi) }}" target="_blank" class="btn btn-success btn-sm mr-2">
            <i class="fas fa-print mr-1"></i> Cetak
          </a>
          <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
          </a>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-8">
          <div class="card shadow-sm">
            <div class="card-header bg-light">
              <h5 class="card-title mb-0"><i class="fas fa-info-circle mr-2"></i>Informasi Transaksi</h5>
            </div>
            <div class="card-body">
              <div class="row mb-3">
                <div class="col-sm-4"><strong>ID Transaksi:</strong></div>
                <div class="col-sm-8">{{ $trx->id_transaksi }}</div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Tanggal:</strong></div>
                <div class="col-sm-8">{{ ($hariIndo[date('l', strtotime($trx->tanggal))] ?? '') . ', ' . date('d-m-Y', strtotime($trx->tanggal)) }}</div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Nama Barang:</strong></div>
                <div class="col-sm-8">{{ $trx->barangRel->nama_barang ?? '-' }}</div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Serial Number:</strong></div>
                <div class="col-sm-8">{{ $sn }}</div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Jumlah:</strong></div>
                <div class="col-sm-8">{{ number_format($trx->jumlah) . ' ' . ($trx->barangRel->satuanRel->nama_satuan ?? '') }}</div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Jenis Transaksi:</strong></div>
                <div class="col-sm-8"><span class="badge badge-primary">{{ $trx->jenis }}</span></div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Status:</strong></div>
                <div class="col-sm-8">
                  @if($trx->status === 'Disetujui')
                    <span class="badge badge-success">Disetujui</span>
                  @elseif($trx->status === 'Ditolak')
                    <span class="badge badge-danger">Ditolak</span>
                  @elseif($trx->status === 'Dipinjam')
                    <span class="badge badge-warning">Dipinjam</span>
                  @elseif($trx->status === 'Dikembalikan')
                    <span class="badge badge-success">Dikembalikan</span>
                  @else
                    <span class="badge badge-secondary">{{ $trx->status }}</span>
                  @endif
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Lokasi Rak:</strong></div>
                <div class="col-sm-8">
                  @if($trx->rak)
                    {{ $trx->rak->nama_rak }}
                    @if($trx->rak->lokasi)
                      <br><small class="text-muted">{{ $trx->rak->lokasi }}</small>
                    @endif
                  @else
                    <span class="text-muted">Tidak ditentukan</span>
                  @endif
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Keranjang:</strong></div>
                <div class="col-sm-8">
                  @if($trx->keranjang)
                    {{ $trx->keranjang->nama_keranjang }}
                  @else
                    <span class="text-muted">Tidak ditentukan</span>
                  @endif
                </div>
              </div>

              @if(!empty($trx->tanggal_pengembalian) || !empty($trx->kondisi))
              <hr>
              <h6><i class="fas fa-undo mr-2"></i>Informasi Pengembalian</h6>
              @if(!empty($trx->tanggal_pengembalian))
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Tanggal Pengembalian:</strong></div>
                <div class="col-sm-8">{{ ($hariIndo[date('l', strtotime($trx->tanggal_pengembalian))] ?? '') . ', ' . date('d-m-Y', strtotime($trx->tanggal_pengembalian)) }}</div>
              </div>
              @endif
              @if(!empty($trx->kondisi))
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Kondisi:</strong></div>
                <div class="col-sm-8">
                  <span class="badge badge-{{ $trx->kondisi === 'Baik' ? 'success' : ($trx->kondisi === 'Rusak' ? 'warning' : 'danger') }}">{{ $trx->kondisi }}</span>
                </div>
              </div>
              @endif
              @endif

              @if(!empty($trx->catatan))
              <hr>
              <h6><i class="fas fa-sticky-note mr-2"></i>Catatan Transaksi</h6>
              <div class="row mb-3">
                <div class="col-sm-4"><strong>Catatan:</strong></div>
                <div class="col-sm-8">{!! nl2br(e($trx->catatan)) !!}</div>
              </div>
              @endif

              {{-- Form update status (POST, seperti handler POST proses_status.php) --}}
              @if($canApprove && $trx->status === 'Menunggu Persetujuan')
              <hr>
              <h6><i class="fas fa-check-circle mr-2"></i>Update Status</h6>
              <form action="{{ route('barang-keluar.status') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $trx->id_transaksi }}">
                <div class="form-group">
                  <label>Catatan Admin</label>
                  <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan persetujuan/penolakan (opsional)"></textarea>
                </div>
                <button type="submit" name="status" value="Disetujui" class="btn btn-success btn-sm mr-2"
                        onclick="return confirm('Setujui barang keluar {{ $trx->id_transaksi }}?')">
                  <i class="fas fa-check fa-sm mr-1"></i> Setujui
                </button>
                <button type="submit" name="status" value="Ditolak" class="btn btn-warning btn-sm"
                        onclick="return confirm('Tolak barang keluar {{ $trx->id_transaksi }}?')">
                  <i class="fas fa-times fa-sm mr-1"></i> Tolak
                </button>
              </form>
              @endif
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
              <h6 class="card-title mb-0"><i class="fas fa-image mr-2"></i>Foto Barang</h6>
            </div>
            <div class="card-body text-center">
              @if($foto && file_exists(public_path('images/'.$foto)))
                <img src="{{ asset('images/'.$foto) }}" alt="Foto Barang" class="img-fluid rounded shadow"
                     style="max-height:200px; cursor:pointer;" onclick="tampilGambar('{{ asset('images/'.$foto) }}')">
              @else
                <div class="d-flex align-items-center justify-content-center" style="height:150px; background:#f8f9fa; border:2px dashed #dee2e6; border-radius:5px;">
                  <div class="text-center">
                    <i class="fas fa-image fa-3x text-muted mb-2"></i>
                    <p class="text-muted">Tidak ada foto</p>
                  </div>
                </div>
              @endif
            </div>
          </div>

          <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
              <h6 class="card-title mb-0"><i class="fas fa-barcode mr-2"></i>Barcode</h6>
            </div>
            <div class="card-body text-center">
              <div class="mb-2"><strong>{{ $sn }}</strong></div>
              <div style="border:1px solid #ccc; padding:8px; text-align:center; font-family:monospace; background:#f9f9f9;">{{ $sn }}</div>
            </div>
          </div>

          @php
            $fotoPeng = $trx->foto_pengembalian;
            $fotoPengUrl = null;
            if ($fotoPeng && file_exists(public_path('images/pengembalian/'.$fotoPeng))) {
              $fotoPengUrl = asset('images/pengembalian/'.$fotoPeng);
            } elseif ($fotoPeng && file_exists(public_path('images/'.$fotoPeng))) {
              $fotoPengUrl = asset('images/'.$fotoPeng);
            }
          @endphp
          @if($fotoPengUrl)
          <div class="card shadow-sm">
            <div class="card-header bg-light">
              <h6 class="card-title mb-0"><i class="fas fa-camera mr-2"></i>Foto Pengembalian</h6>
            </div>
            <div class="card-body text-center">
              <img src="{{ $fotoPengUrl }}" alt="Foto Pengembalian" class="img-fluid rounded shadow"
                   style="max-height:200px; cursor:pointer; object-fit:cover;" onclick="tampilGambar('{{ $fotoPengUrl }}')">
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalGambar" tabindex="-1" role="dialog" aria-labelledby="modalGambarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalGambarLabel">Preview Gambar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <img id="previewImg" src="" alt="Preview" class="img-fluid rounded shadow">
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script>
function tampilGambar(src) {
  document.getElementById("previewImg").src = src;
  $('#modalGambar').modal('show');
}
</script>
@endpush
