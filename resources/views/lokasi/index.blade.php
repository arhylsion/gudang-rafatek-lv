@extends('layouts.app')
@section('title', 'Lokasi Rak')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-45">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-server"></i> Lokasi Rak</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="{{ route('lokasi.index') }}" class="text-white">Lokasi Rak</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Overview</a></li>
        </ul>
      </div>
      <div class="ml-md-auto py-2 py-md-0">
        <a href="{{ route('rak.index') }}" class="btn btn-secondary btn-round mr-2">
          <span class="btn-label"><i class="fas fa-warehouse mr-2"></i></span> Kelola Rak
        </a>
        <a href="{{ route('keranjang.index') }}" class="btn btn-success btn-round">
          <span class="btn-label"><i class="fas fa-shopping-basket mr-2"></i></span> Kelola Keranjang
        </a>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <!-- Summary Cards -->
  <div class="row justify-content-center">
    <div class="col-sm-6 col-md-4">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-icon">
              <div class="icon-big text-center icon-primary bubble-shadow-small">
                <i class="fas fa-warehouse"></i>
              </div>
            </div>
            <div class="col col-stats ml-3 ml-sm-0">
              <div class="numbers">
                <p class="card-category">Total Rak</p>
                <h4 class="card-title">{{ $totalRak }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-icon">
              <div class="icon-big text-center icon-success bubble-shadow-small">
                <i class="fas fa-shopping-basket"></i>
              </div>
            </div>
            <div class="col col-stats ml-3 ml-sm-0">
              <div class="numbers">
                <p class="card-category">Total Keranjang</p>
                <h4 class="card-title">{{ $totalKeranjang }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-icon">
              <div class="icon-big text-center icon-info bubble-shadow-small">
                <i class="fas fa-calculator"></i>
              </div>
            </div>
            <div class="col col-stats ml-3 ml-sm-0">
              <div class="numbers">
                <p class="card-category">Total Kapasitas Rak</p>
                <h4 class="card-title">{{ $totalKapasitas }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Recent Racks -->
  <div class="row">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <div class="card-head-row">
            <div class="card-title">Rak Terbaru</div>
            <div class="card-tools">
              <a href="{{ route('rak.index') }}" class="btn btn-info btn-border btn-round btn-sm">
                <span class="btn-label"><i class="fa fa-eye"></i></span> Lihat Semua
              </a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Kode</th>
                  <th>Nama Rak</th>
                  <th>Lokasi</th>
                  <th>Kapasitas</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentRak as $r)
                  <tr>
                    <td><strong>{{ $r->kode_rak }}</strong></td>
                    <td>{{ $r->nama_rak }}</td>
                    <td>{{ $r->lokasi ?: '-' }}</td>
                    <td class="text-center">{{ $r->kapasitas }}</td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center">Tidak ada data rak.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Baskets -->
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <div class="card-head-row">
            <div class="card-title">Data Keranjang</div>
            <div class="card-tools">
              <a href="{{ route('keranjang.index') }}" class="btn btn-success btn-border btn-round btn-sm">
                <span class="btn-label"><i class="fa fa-eye"></i></span> Lihat Semua
              </a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
              <thead>
                <tr>
                  <th>Kode</th>
                  <th>Nama Keranjang</th>
                  <th>Rak</th>
                  <th>Kondisi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($keranjangs as $k)
                  <tr>
                    <td><strong>{{ $k->kode_keranjang }}</strong></td>
                    <td>{{ $k->nama_keranjang }}</td>
                    <td>{{ $k->rak->nama_rak ?? '-' }}</td>
                    <td>{{ $k->kondisi }}</td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center">Tidak ada data keranjang.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
