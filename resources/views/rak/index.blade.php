@extends('layouts.app')
@section('title', 'Data Rak')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-45">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-warehouse mr-2"></i> Data Rak</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="{{ route('rak.index') }}" class="text-white">Data Rak</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Data</a></li>
        </ul>
      </div>
      <div class="ml-md-auto py-2 py-md-0">
        <a href="{{ route('rak.create') }}" class="btn btn-secondary btn-round mr-2">
          <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data
        </a>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Data Rak</div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center">No.</th>
              <th class="text-center">Kode Rak</th>
              <th class="text-center">Nama Rak</th>
              <th class="text-center">Lokasi</th>
              <th class="text-center">Kapasitas</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rak as $i => $r)
              <tr class="accordion-toggle collapsed" data-target="#aksi-{{ $r->id_rak }}" aria-expanded="false" style="cursor:pointer;">
                <td width="30" class="text-center">{{ $i + 1 }}</td>
                <td width="100" class="text-center">{{ $r->kode_rak }}</td>
                <td width="200">{{ $r->nama_rak }}</td>
                <td width="250">{{ $r->lokasi ?: '-' }}</td>
                <td width="80" class="text-center">{{ $r->kapasitas }}</td>
              </tr>
              <tr class="hide-table-padding aksi-row" style="display:none;">
                <td></td>
                <td colspan="4" style="border-top:1px solid #dee2e6; background:#f8f9fa; padding:16px 24px;">
                  <div id="aksi-{{ $r->id_rak }}">
                    <div class="row">
                      <div class="col-12">
                        @if($r->keterangan)
                          <p class="text-muted mb-3">{{ $r->keterangan }}</p>
                        @endif
                        <div class="d-flex flex-wrap">
                          <a href="{{ route('rak.edit', $r->id_rak) }}" class="btn btn-secondary btn-sm mr-2 mb-2">
                            <i class="fas fa-pencil-alt fa-sm mr-1"></i> Ubah
                          </a>
                          <form action="{{ route('rak.destroy', $r->id_rak) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus data rak {{ addslashes($r->nama_rak) }}?')">
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
