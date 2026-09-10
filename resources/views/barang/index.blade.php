@extends('layouts.app')
@section('title', 'Data Barang')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-45">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-clone mr-2"></i> Barang</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="{{ route('barang.index') }}" class="text-white">Barang</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Data</a></li>
        </ul>
      </div>
      <div class="ml-md-auto py-2 py-md-0">
        <a href="{{ route('barang.create') }}" class="btn btn-secondary btn-round">
          <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data
        </a>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Data Barang</div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <div class="mb-3">
          <form method="GET" action="{{ route('barang.index') }}" id="searchForm">
            <div class="input-group">
              <input type="text" name="search" id="searchInput" class="form-control" placeholder="Cari Data Barang..." value="{{ $search }}">
            </div>
          </form>
        </div>
        <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center" width="30">No</th>
              <th class="text-center" width="150">Serial Number</th>
              <th class="text-center" width="200">Nama Barang</th>
              <th class="text-center" width="80">Stok</th>
              <th class="text-center" width="100">Satuan</th>
              <th class="text-center" width="150">Lokasi Rak</th>
              <th class="text-center" width="120">Keranjang</th>
              <th class="text-center" width="120">Foto</th>
            </tr>
          </thead>
          <tbody>
            @forelse($barangs as $i => $b)
              <tr class="accordion-toggle collapsed" data-target="#aksi-{{ $b->id_barang }}" aria-expanded="false" style="cursor:pointer;">
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="text-center">
                  <div style="padding: 5px;">
                    <div style="font-weight: 600; margin-bottom: 5px;">{{ $b->serial_number ?: $b->id_barang }}</div>
                  </div>
                </td>
                <td>{{ $b->nama_barang }}</td>
                <td class="text-right">{{ number_format($b->stok) }}</td>
                <td class="text-center">{{ $b->satuanRel->nama_satuan ?? '-' }}</td>
                <td class="text-center">
                  @if($b->rak)
                    <div style="font-weight: 600; color: #495057;">{{ $b->rak->nama_rak }}</div>
                    <small class="text-muted">{{ $b->rak->lokasi }}</small>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($b->keranjang)
                    <div style="font-weight: 600; color: #495057;">{{ $b->keranjang->nama_keranjang }}</div>
                    <small class="text-muted">Kondisi: {{ $b->keranjang->kondisi }}</small>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($b->foto && file_exists(public_path('images/'.$b->foto)))
                    <img src="{{ asset('images/'.$b->foto) }}" alt="Foto Barang"
                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer; border: 2px solid #dee2e6;"
                         onclick="event.stopPropagation(); tampilGambar('{{ asset('images/'.$b->foto) }}')">
                  @elseif($b->foto)
                    <div style="width: 60px; height: 60px; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 5px; display: flex; align-items: center; justify-content: center; margin: 0 auto;" title="Foto tidak ditemukan: {{ $b->foto }}">
                      <i class="fas fa-image text-muted"></i>
                    </div>
                  @else
                    <img src="{{ asset('images/no_image.png') }}" alt="Tidak ada foto"
                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; border: 2px solid #dee2e6; opacity: 0.7;">
                  @endif
                </td>
              </tr>
              <tr class="hide-table-padding aksi-row" style="display:none;">
                <td></td>
                <td colspan="7" style="border-top:1px solid #dee2e6; background:#f8f9fa; padding:16px 24px;">
                  <div id="aksi-{{ $b->id_barang }}">
                    <div class="row">
                      <div class="col-12">
                        <div class="d-flex flex-wrap">
                          <a href="{{ route('barang.show', $b->id_barang) }}" class="btn btn-primary btn-sm mr-2 mb-2">
                            <i class="fas fa-clone fa-sm mr-1"></i> Detail
                          </a>
                          <a href="{{ route('barang.edit', $b->id_barang) }}" class="btn btn-secondary btn-sm mr-2 mb-2">
                            <i class="fas fa-pencil-alt fa-sm mr-1"></i> Ubah
                          </a>
                          <form action="{{ route('barang.destroy', $b->id_barang) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Anda yakin ingin menghapus data barang {{ addslashes($b->nama_barang) }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm mb-2">
                              <i class="fas fa-trash fa-sm mr-1"></i> Hapus
                            </button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="8" class="text-center">Tidak ada data yang ditemukan.</td></tr>
            @endforelse
          </tbody>
        </table>
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

<style>
  .hide-table-padding td { padding: 0 !important; background: #fff; }
  .aksi-row { background: #f8f9fa; }
  .accordion-toggle.table-active,
  .accordion-toggle:active,
  .accordion-toggle:focus { background: #e9ecef !important; }
  .accordion-toggle:hover { background: #f8f9fa !important; }
</style>
@endsection

@push('js')
<script>
function tampilGambar(src) {
  document.getElementById("previewImg").src = src;
  $('#modalGambar').modal('show');
}

$(document).ready(function() {
  var searchTimeout;
  $('#searchInput').on('input', function() {
    clearTimeout(searchTimeout);
    var searchTerm = $(this).val();
    if (searchTerm.length === 0 || searchTerm.length >= 2) {
      searchTimeout = setTimeout(function() {
        $('#searchForm').submit();
      }, 300);
    }
  });
});

document.addEventListener('DOMContentLoaded', function() {
  var toggles = document.querySelectorAll('.accordion-toggle');
  var aksiRows = document.querySelectorAll('.aksi-row');
  var lastOpen = null;

  toggles.forEach(function(toggle) {
    toggle.addEventListener('click', function(e) {
      var next = toggle.nextElementSibling;
      aksiRows.forEach(function(row) { row.style.display = 'none'; });
      toggles.forEach(function(t) { t.classList.remove('table-active'); t.classList.add('collapsed'); });
      if (lastOpen === next) {
        lastOpen = null;
      } else {
        next.style.display = 'table-row';
        toggle.classList.add('table-active');
        toggle.classList.remove('collapsed');
        lastOpen = next;
      }
      e.stopPropagation();
    });
  });

  document.addEventListener('click', function(e) {
    if (!e.target.closest('.accordion-toggle') && !e.target.closest('.aksi-row')) {
      aksiRows.forEach(function(row) { row.style.display = 'none'; });
      toggles.forEach(function(t) { t.classList.remove('table-active'); t.classList.add('collapsed'); });
      lastOpen = null;
    }
  });
});
</script>
@endpush
