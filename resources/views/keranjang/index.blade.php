@extends('layouts.app')
@section('title', 'Data Keranjang')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-45">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-shopping-basket mr-2"></i> Data Keranjang</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="{{ route('keranjang.index') }}" class="text-white">Data Keranjang</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Data</a></li>
        </ul>
      </div>
      <div class="ml-md-auto py-2 py-md-0">
        <a href="{{ route('keranjang.create') }}" class="btn btn-secondary btn-round mr-2">
          <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data
        </a>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Data Keranjang</div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center">No.</th>
              <th class="text-center">Kode Keranjang</th>
              <th class="text-center">Nama Keranjang</th>
              <th class="text-center">Rak</th>
            </tr>
          </thead>
          <tbody>
            @foreach($keranjang as $i => $k)
              <tr class="accordion-toggle collapsed" data-target="#aksi-{{ $k->id_keranjang }}" aria-expanded="false" style="cursor:pointer;">
                <td width="30" class="text-center">{{ $i + 1 }}</td>
                <td width="100" class="text-center">{{ $k->kode_keranjang }}</td>
                <td width="200">{{ $k->nama_keranjang }}</td>
                <td width="150">{{ $k->rak->nama_rak ?? '-' }}</td>
              </tr>
              <tr class="hide-table-padding aksi-row" style="display:none;">
                <td></td>
                <td colspan="3" style="border-top:1px solid #dee2e6; background:#f8f9fa; padding:16px 24px;">
                  <div id="aksi-{{ $k->id_keranjang }}">
                    <div class="row">
                      <div class="col-12">
                        <p class="text-muted mb-1">Kapasitas: <strong>{{ $k->kapasitas }}</strong> &nbsp;|&nbsp; Kondisi: <strong>{{ $k->kondisi }}</strong></p>
                        @if($k->keterangan)
                          <p class="text-muted mb-3">{{ $k->keterangan }}</p>
                        @endif
                        <div class="d-flex flex-wrap">
                          <a href="{{ route('keranjang.edit', $k->id_keranjang) }}" class="btn btn-secondary btn-sm mr-2 mb-2">
                            <i class="fas fa-pencil-alt fa-sm mr-1"></i> Ubah
                          </a>
                          <form action="{{ route('keranjang.destroy', $k->id_keranjang) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus data keranjang {{ addslashes($k->nama_keranjang) }}?')">
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
            @endforeach
          </tbody>
        </table>
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
