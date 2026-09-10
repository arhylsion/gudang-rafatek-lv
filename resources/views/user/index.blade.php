@extends('layouts.app')
@section('title', 'Manajemen User')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-45">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-user mr-2"></i> Manajemen User</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="{{ route('user.index') }}" class="text-white">User</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Data</a></li>
        </ul>
      </div>
      <div class="ml-md-auto py-2 py-md-0">
        <a href="{{ route('user.create') }}" class="btn btn-secondary btn-round mr-2">
          <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data
        </a>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Data User</div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center">No.</th>
              <th class="text-center">Nama User</th>
              <th class="text-center">Username</th>
              <th class="text-center">Email</th>
              <th class="text-center">Hak Akses</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $i => $u)
              <tr>
                <td width="30" class="text-center">{{ $i + 1 }}</td>
                <td width="180">{{ $u->nama_user }}</td>
                <td width="180">{{ $u->username }}</td>
                <td width="200">{{ $u->email }}</td>
                <td width="100">{{ $u->hak_akses }}</td>
                <td width="90" class="text-center">
                  <div>
                    <a href="{{ route('profil.edit', ['id' => $u->id_user]) }}" class="btn btn-icon btn-round btn-info btn-sm mr-md-1" data-toggle="tooltip" data-placement="top" title="Edit">
                      <i class="fas fa-user-edit fa-sm"></i>
                    </a>
                    <form action="{{ route('user.destroy', $u->id_user) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus data user dengan username {{ addslashes($u->username) }}?')">
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
