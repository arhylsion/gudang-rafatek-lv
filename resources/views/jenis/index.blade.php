@extends('layouts.app')
@section('title', 'Jenis Barang')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-45">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-clone mr-2"></i> Jenis Barang</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="{{ route('jenis.index') }}" class="text-white">Jenis Barang</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Data</a></li>
        </ul>
      </div>
      <div class="ml-md-auto py-2 py-md-0">
        <a href="{{ route('jenis.create') }}" class="btn btn-secondary btn-round mr-2">
          <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data
        </a>
        <a href="{{ route('jenis.export') }}" class="btn btn-success btn-round">
          <span class="btn-label"><i class="fa fa-file-excel mr-2"></i></span> Export
        </a>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Data Jenis Barang</div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center">No.</th>
              <th class="text-center">Jenis Barang</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($jenis as $i => $j)
              <tr>
                <td width="30" class="text-center">{{ $i + 1 }}</td>
                <td width="300">{{ $j->nama_jenis }}</td>
                <td width="70" class="text-center">
                  <div>
                    <a href="{{ route('jenis.edit', $j->id_jenis) }}" class="btn btn-icon btn-round btn-secondary btn-sm mr-md-1" data-toggle="tooltip" data-placement="top" title="Ubah">
                      <i class="fas fa-pencil-alt fa-sm"></i>
                    </a>
                    <form action="{{ route('jenis.destroy', $j->id_jenis) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus data jenis barang {{ addslashes($j->nama_jenis) }}?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-icon btn-round btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Hapus">
                        <i class="fas fa-trash fa-sm"></i>
                      </button>
                    </form>
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
@endsection
