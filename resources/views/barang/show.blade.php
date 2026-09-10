@extends('layouts.app')
@section('title', 'Detail Barang')

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
          <li class="nav-item"><a>Detail</a></li>
        </ul>
      </div>
      <div class="ml-md-auto py-2 py-md-0">
        <a href="{{ route('barang.index') }}" class="btn btn-secondary btn-round">
          <span class="btn-label"><i class="far fa-arrow-alt-circle-left mr-2"></i></span> Kembali
        </a>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Detail Data Barang</div>
        </div>
        <div class="card-body">
          <table class="table table-striped">
            <tr>
              <td width="120">ID Barang</td>
              <td width="10">:</td>
              <td>{{ $barang->id_barang }}</td>
            </tr>
            <tr>
              <td>Nama Barang</td>
              <td>:</td>
              <td>{{ $barang->nama_barang }}</td>
            </tr>
            @if($barang->serial_number)
            <tr>
              <td>Serial Number</td>
              <td>:</td>
              <td>{{ $barang->serial_number }}</td>
            </tr>
            @endif
            <tr>
              <td>Jenis Barang</td>
              <td>:</td>
              <td>{{ $barang->jenisRel->nama_jenis ?? '-' }}</td>
            </tr>
            <tr>
              <td>Stok Minimum</td>
              <td>:</td>
              <td>{{ $barang->stok_minimum }}</td>
            </tr>
            <tr>
              <td>Stok</td>
              <td>:</td>
              <td>{{ $barang->stok }}</td>
            </tr>
            <tr>
              <td>Satuan</td>
              <td>:</td>
              <td>{{ $barang->satuanRel->nama_satuan ?? '-' }}</td>
            </tr>
            @if($barang->rak)
            <tr>
              <td>Lokasi Rak</td>
              <td>:</td>
              <td>{{ $barang->rak->nama_rak }} <small class="text-muted">({{ $barang->rak->lokasi }})</small></td>
            </tr>
            @endif
            @if($barang->keranjang)
            <tr>
              <td>Keranjang</td>
              <td>:</td>
              <td>{{ $barang->keranjang->nama_keranjang }} <small class="text-muted">(Kondisi: {{ $barang->keranjang->kondisi }})</small></td>
            </tr>
            @endif
            <tr>
              <td>Status</td>
              <td>:</td>
              <td>
                @php $jenisNama = $barang->jenisRel->nama_jenis ?? ''; @endphp
                @if(stripos($jenisNama, 'modem') !== false && $barang->stok <= 0)
                  <span class="badge badge-success">On Use</span>
                @else
                  <span class="badge badge-primary">On Stock</span>
                @endif
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body text-center">
          @if($barang->foto && file_exists(public_path('images/'.$barang->foto)))
            <img style="max-height:375px" src="{{ asset('images/'.$barang->foto) }}" class="img-fluid" alt="Foto Barang">
          @else
            <img style="max-height:375px" src="{{ asset('images/no_image.png') }}" class="img-fluid" alt="Foto Barang">
            @if($barang->foto)
              <div class="mt-2">
                <small class="text-muted">Foto tidak ditemukan: {{ $barang->foto }}</small>
              </div>
            @endif
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
