@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@if(request()->has('pesan') && request('pesan')==1 && !session('success'))
<div class="alert alert-notify alert-secondary alert-dismissible fade show" role="alert">
  <span data-notify="icon" class="fas fa-user-alt"></span>
  <span data-notify="title" class="text-secondary">Hi! {{ Auth::user()->nama_user }}</span>
  <span data-notify="message">Selamat Datang di Aplikasi Persediaan Barang Gudang Material.</span>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
@endif

<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-5">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white"><h4 class="page-title text-white"><i class="fas fa-home mr-2"></i> Dashboard</h4></div>
    </div>
  </div>
</div>
<div class="page-inner mt--5">
  <div class="row row-card-no-pd mt--2">
    <div class="col-sm-12 col-md-4">
      <div class="card card-stats card-round"><div class="card-body"><div class="row">
        <div class="col-5"><div class="icon-big2 text-center"><i class="flaticon-box-2 text-secondary"></i></div></div>
        <div class="col-7 col-stats"><div class="numbers"><p class="card-category">Data Barang</p><h4 class="card-title">{{ number_format($jumlahBarang,0,'','.') }}</h4></div></div>
      </div></div></div>
    </div>
    <div class="col-sm-12 col-md-4">
      <div class="card card-stats card-round"><div class="card-body"><div class="row">
        <div class="col-5"><div class="icon-big2 text-center"><i class="flaticon-inbox text-success"></i></div></div>
        <div class="col-7 col-stats"><div class="numbers"><p class="card-category">Data Barang Masuk</p><h4 class="card-title">{{ number_format($jumlahMasuk,0,'','.') }}</h4></div></div>
      </div></div></div>
    </div>
    <div class="col-sm-12 col-md-4">
      <div class="card card-stats card-round"><div class="card-body"><div class="row">
        <div class="col-5"><div class="icon-big2 text-center"><i class="flaticon-archive text-warning"></i></div></div>
        <div class="col-7 col-stats"><div class="numbers"><p class="card-category">Data Barang Keluar</p><h4 class="card-title">{{ number_format($jumlahKeluar,0,'','.') }}</h4></div></div>
      </div></div></div>
    </div>
  </div>

  @if(Auth::user()->hak_akses != 'Kepala Gudang')
  <div class="row">
    <div class="col-sm-12 col-md-4">
      <div class="card card-stats card-round"><div class="card-body"><div class="row align-items-center">
        <div class="col-icon"><div class="icon-big text-center icon-warning bubble-shadow-small"><i class="fas fa-clone"></i></div></div>
        <div class="col col-stats ml-3 ml-sm-0"><div class="numbers"><p class="card-category">Data Jenis Barang</p><h4 class="card-title">{{ number_format($jumlahJenis,0,'','.') }}</h4></div></div>
      </div></div></div>
    </div>
    <div class="col-sm-12 col-md-4">
      <div class="card card-stats card-round"><div class="card-body"><div class="row align-items-center">
        <div class="col-icon"><div class="icon-big text-center icon-info bubble-shadow-small"><i class="fas fa-folder-open"></i></div></div>
        <div class="col col-stats ml-3 ml-sm-0"><div class="numbers"><p class="card-category">Data Satuan</p><h4 class="card-title">{{ number_format($jumlahSatuan,0,'','.') }}</h4></div></div>
      </div></div></div>
    </div>
    <div class="col-sm-12 col-md-4">
      <div class="card card-stats card-round"><div class="card-body"><div class="row align-items-center">
        <div class="col-icon"><div class="icon-big text-center icon-success bubble-shadow-small"><i class="fas fa-user-circle"></i></div></div>
        <div class="col col-stats ml-3 ml-sm-0"><div class="numbers"><p class="card-category">Data User</p><h4 class="card-title">{{ number_format($jumlahUser,0,'','.') }}</h4></div></div>
      </div></div></div>
    </div>
  </div>
  <hr class="mt-1 pb-2">
  @endif

  <div class="row">
    <div class="col-md-12">
      <div class="card"><div class="card-header"><div class="card-title"><i class="fas fa-chart-line mr-2"></i> Grafik Barang Masuk & Keluar (12 Bulan Terakhir)</div></div>
        <div class="card-body"><div class="chart-container" style="position: relative; height: 400px;"><canvas id="combinationChart"></canvas></div></div>
      </div>
    </div>
  </div>
  <hr class="mt-3 mb-3">
  <div class="row">
    @forelse($jenisList as $idx => $jenis)
    <div class="col-md-6 col-lg-6">
      <div class="card"><div class="card-header"><div class="card-title"><i class="fas fa-chart-bar mr-2"></i> {{ $jenis->nama_jenis }}</div></div>
        <div class="card-body"><div class="chart-container" style="position: relative; height: 300px;"><canvas id="categoryChart{{ $idx }}"></canvas></div></div>
      </div>
    </div>
    @empty
    <div class="col-md-12"><div class="alert alert-info">Belum ada data jenis barang.</div></div>
    @endforelse
  </div>
  <hr class="mt-3 mb-3">

  <div class="card">
    <div class="card-header"><div class="card-title"><i class="fas fa-boxes mr-2"></i> Stok barang tersedia</div></div>
    <div class="card-body"><div class="table-responsive">
      <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
        <thead><tr><th class="text-center">No.</th><th class="text-center">Foto</th><th class="text-center">Serial Number</th><th class="text-center">Nama Barang</th><th class="text-center">Jenis Barang</th><th class="text-center">Stok</th><th class="text-center">Satuan</th></tr></thead>
        <tbody>
          @foreach($stokTersedia as $i => $d)
          <tr>
            <td width="50" class="text-center">{{ $i+1 }}</td>
            <td width="80" class="text-center">
              @if($d->foto && file_exists(public_path('images/'.$d->foto)))
                <img src="{{ asset('images/'.$d->foto) }}" alt="{{ $d->nama_barang }}" width="60" height="60" style="object-fit:cover; border-radius:8px;">
              @else<span class="badge badge-secondary">No Image</span>@endif
            </td>
            <td width="150" class="text-center">{{ strtoupper($d->jenisRel->nama_jenis ?? '')=='MODEM' && $d->serial_number ? $d->serial_number : $d->id_barang }}</td>
            <td width="200">{{ $d->nama_barang }}</td>
            <td width="150">{{ $d->jenisRel->nama_jenis ?? '-' }}</td>
            <td width="70" class="text-right"><span class="badge badge-success">{{ $d->stok }}</span></td>
            <td width="70">{{ $d->satuanRel->nama_satuan ?? '-' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div></div>
  </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const ctx = document.getElementById('combinationChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: { labels: @json($months), datasets: [
      { label: 'Jumlah Transaksi Masuk', data: @json($dataMasuk), backgroundColor: 'rgba(40,167,69,0.8)', borderWidth: 1, yAxisID: 'y' },
      { label: 'Jumlah Transaksi Keluar', data: @json($dataKeluar), backgroundColor: 'rgba(255,193,7,0.8)', borderWidth: 1, yAxisID: 'y' },
      { label: 'Total Jumlah Masuk', data: @json($totalMasuk), backgroundColor: 'rgba(23,162,184,0.8)', borderWidth: 1, yAxisID: 'y1' },
      { label: 'Total Jumlah Keluar', data: @json($totalKeluar), backgroundColor: 'rgba(220,53,69,0.8)', borderWidth: 1, yAxisID: 'y1' },
    ]},
    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true }, y1: { beginAtZero: true, position: 'right' } } }
  });
  const categoryData = @json($categoryData);
  const months = @json($months);
  const colors = ['rgba(54,162,235,0.8)','rgba(255,99,132,0.8)','rgba(75,192,192,0.8)','rgba(153,102,255,0.8)','rgba(255,159,64,0.8)'];
  let idx = 0;
  for (const [cat, data] of Object.entries(categoryData)) {
    const el = document.getElementById('categoryChart'+idx);
    if (el) {
      new Chart(el.getContext('2d'), { type: 'bar',
        data: { labels: months, datasets: [
          { label: 'Masuk', data: data.masuk, backgroundColor: colors[idx%colors.length] },
          { label: 'Keluar', data: data.keluar, backgroundColor: 'rgba(201,203,207,0.8)' },
        ]},
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
      });
    }
    idx++;
  }
});
</script>
@endpush
