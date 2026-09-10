@php
  $akses = Auth::user()->hak_akses ?? '';
  $route = Route::currentRouteName() ?? '';
  $is = fn($names) => in_array($route, (array)$names);
@endphp

@if($akses == 'Administrator')
  <li class="nav-item {{ $route=='dashboard' ? 'active' : '' }}">
    <a href="{{ route('dashboard') }}"><i class="fas fa-home"></i><p>Dashboard</p></a>
  </li>
  <li class="nav-section"><span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span><h4 class="text-section">Master</h4></li>
  <li class="nav-item submenu {{ in_array($route, ['barang.index','barang.show','barang.create','barang.edit','jenis.index','jenis.create','jenis.edit','satuan.index','satuan.create','satuan.edit']) ? 'active' : '' }}">
    <a data-toggle="collapse" href="#barang"><i class="fas fa-clone"></i><p>Barang</p><span class="caret"></span></a>
    <div class="collapse {{ in_array($route, ['barang.index','barang.show','barang.create','barang.edit','jenis.index','satuan.index']) ? 'show' : '' }}" id="barang">
      <ul class="nav nav-collapse">
        <li class="{{ $is(['barang.index','barang.show','barang.create','barang.edit']) ? 'active' : '' }}"><a href="{{ route('barang.index') }}"><span class="sub-item">Data Barang</span></a></li>
        <li class="{{ $is(['jenis.index','jenis.create','jenis.edit']) ? 'active' : '' }}"><a href="{{ route('jenis.index') }}"><span class="sub-item">Jenis Barang</span></a></li>
        <li class="{{ $is(['satuan.index','satuan.create','satuan.edit']) ? 'active' : '' }}"><a href="{{ route('satuan.index') }}"><span class="sub-item">Satuan</span></a></li>
      </ul>
    </div>
  </li>
  <li class="nav-item submenu {{ in_array($route, ['lokasi.index','rak.index','rak.create','rak.edit','keranjang.index','keranjang.create','keranjang.edit']) ? 'active' : '' }}">
    <a data-toggle="collapse" href="#lokasi"><i class="fas fa-server"></i><p>Lokasi Rak</p><span class="caret"></span></a>
    <div class="collapse {{ in_array($route, ['lokasi.index','rak.index','keranjang.index']) ? 'show' : '' }}" id="lokasi">
      <ul class="nav nav-collapse">
        <li class="{{ $route=='lokasi.index' ? 'active' : '' }}"><a href="{{ route('lokasi.index') }}"><span class="sub-item">Lokasi Rak</span></a></li>
        <li class="{{ $is(['rak.index','rak.create','rak.edit']) ? 'active' : '' }}"><a href="{{ route('rak.index') }}"><span class="sub-item">Data Rak</span></a></li>
        <li class="{{ $is(['keranjang.index','keranjang.create','keranjang.edit']) ? 'active' : '' }}"><a href="{{ route('keranjang.index') }}"><span class="sub-item">Data Keranjang</span></a></li>
      </ul>
    </div>
  </li>
  <li class="nav-section"><span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span><h4 class="text-section">Transaksi</h4></li>
  <li class="nav-item {{ $is(['barang-masuk.index','barang-masuk.create']) ? 'active' : '' }}"><a href="{{ route('barang-masuk.index') }}"><i class="fas fa-sign-in-alt"></i><p>Barang Masuk</p></a></li>
  <li class="nav-item {{ $is(['barang-keluar.index','barang-keluar.create']) ? 'active' : '' }}"><a href="{{ route('barang-keluar.index') }}"><i class="fas fa-sign-out-alt"></i><p>Barang Keluar</p></a></li>
  <li class="nav-item {{ $route=='opname.index' ? 'active' : '' }}"><a href="{{ route('opname.index') }}"><i class="fas fa-clipboard-list"></i><p>Stock Opname</p></a></li>
  <li class="nav-section"><span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span><h4 class="text-section">Laporan</h4></li>
  <li class="nav-item {{ $route=='laporan.stok' ? 'active' : '' }}"><a href="{{ route('laporan.stok') }}"><i class="fas fa-file-signature"></i><p>Laporan Stok</p></a></li>
  <li class="nav-item {{ $route=='laporan.masuk' ? 'active' : '' }}"><a href="{{ route('laporan.masuk') }}"><i class="fas fa-file-import"></i><p>Laporan Barang Masuk</p></a></li>
  <li class="nav-item {{ $route=='laporan.keluar' ? 'active' : '' }}"><a href="{{ route('laporan.keluar') }}"><i class="fas fa-file-export"></i><p>Laporan Barang Keluar</p></a></li>
  <li class="nav-section"><span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span><h4 class="text-section">Pengaturan</h4></li>
  <li class="nav-item {{ $is(['user.index','user.create','user.edit']) ? 'active' : '' }}"><a href="{{ route('user.index') }}"><i class="fas fa-user"></i><p>Manajemen User</p></a></li>

@elseif($akses == 'Teknisi')
  <li class="nav-item {{ $route=='dashboard' ? 'active' : '' }}"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i><p>Dashboard</p></a></li>
  <li class="nav-item {{ $is(['barang-keluar.index','barang-keluar.create']) ? 'active' : '' }}"><a href="{{ route('barang-keluar.index') }}"><i class="fas fa-sign-out-alt"></i><p>Barang Keluar</p></a></li>

@elseif($akses == 'Admin Gudang')
  <li class="nav-item {{ $route=='dashboard' ? 'active' : '' }}"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i><p>Dashboard</p></a></li>
  <li class="nav-section"><span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span><h4 class="text-section">Master</h4></li>
  <li class="nav-item submenu {{ in_array($route, ['barang.index','jenis.index','satuan.index']) ? 'active' : '' }}">
    <a data-toggle="collapse" href="#barang"><i class="fas fa-clone"></i><p>Barang</p><span class="caret"></span></a>
    <div class="collapse {{ in_array($route, ['barang.index','jenis.index','satuan.index']) ? 'show' : '' }}" id="barang">
      <ul class="nav nav-collapse">
        <li class="{{ $route=='barang.index' ? 'active' : '' }}"><a href="{{ route('barang.index') }}"><span class="sub-item">Data Barang</span></a></li>
        <li class="{{ $route=='jenis.index' ? 'active' : '' }}"><a href="{{ route('jenis.index') }}"><span class="sub-item">Jenis Barang</span></a></li>
        <li class="{{ $route=='satuan.index' ? 'active' : '' }}"><a href="{{ route('satuan.index') }}"><span class="sub-item">Satuan</span></a></li>
      </ul>
    </div>
  </li>
  <li class="nav-item submenu {{ in_array($route, ['lokasi.index','rak.index','keranjang.index']) ? 'active' : '' }}">
    <a data-toggle="collapse" href="#lokasi_admin"><i class="fas fa-map-marker-alt"></i><p>Lokasi Rak</p><span class="caret"></span></a>
    <div class="collapse {{ in_array($route, ['lokasi.index','rak.index','keranjang.index']) ? 'show' : '' }}" id="lokasi_admin">
      <ul class="nav nav-collapse">
        <li class="{{ $route=='lokasi.index' ? 'active' : '' }}"><a href="{{ route('lokasi.index') }}"><span class="sub-item">Lokasi Rak</span></a></li>
        <li class="{{ $route=='rak.index' ? 'active' : '' }}"><a href="{{ route('rak.index') }}"><span class="sub-item">Data Rak</span></a></li>
        <li class="{{ $route=='keranjang.index' ? 'active' : '' }}"><a href="{{ route('keranjang.index') }}"><span class="sub-item">Data Keranjang</span></a></li>
      </ul>
    </div>
  </li>
  <li class="nav-section"><span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span><h4 class="text-section">Transaksi</h4></li>
  <li class="nav-item {{ $route=='barang-masuk.index' ? 'active' : '' }}"><a href="{{ route('barang-masuk.index') }}"><i class="fas fa-sign-in-alt"></i><p>Barang Masuk</p></a></li>
  <li class="nav-item {{ $route=='barang-keluar.index' ? 'active' : '' }}"><a href="{{ route('barang-keluar.index') }}"><i class="fas fa-sign-out-alt"></i><p>Barang Keluar</p></a></li>
  <li class="nav-item {{ $route=='opname.index' ? 'active' : '' }}"><a href="{{ route('opname.index') }}"><i class="fas fa-clipboard-list"></i><p>Stock Opname</p></a></li>
  <li class="nav-section"><span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span><h4 class="text-section">Laporan</h4></li>
  <li class="nav-item {{ $route=='laporan.stok' ? 'active' : '' }}"><a href="{{ route('laporan.stok') }}"><i class="fas fa-file-signature"></i><p>Laporan Stok</p></a></li>
  <li class="nav-item {{ $route=='laporan.masuk' ? 'active' : '' }}"><a href="{{ route('laporan.masuk') }}"><i class="fas fa-file-import"></i><p>Laporan Barang Masuk</p></a></li>
  <li class="nav-item {{ $route=='laporan.keluar' ? 'active' : '' }}"><a href="{{ route('laporan.keluar') }}"><i class="fas fa-file-export"></i><p>Laporan Barang Keluar</p></a></li>

@elseif($akses == 'Kepala Gudang')
  <li class="nav-item {{ $route=='dashboard' ? 'active' : '' }}"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i><p>Dashboard</p></a></li>
  <li class="nav-section"><span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span><h4 class="text-section">Laporan</h4></li>
  <li class="nav-item {{ $route=='laporan.stok' ? 'active' : '' }}"><a href="{{ route('laporan.stok') }}"><i class="fas fa-file-signature"></i><p>Laporan Stok</p></a></li>
  <li class="nav-item {{ $route=='laporan.masuk' ? 'active' : '' }}"><a href="{{ route('laporan.masuk') }}"><i class="fas fa-file-import"></i><p>Laporan Barang Masuk</p></a></li>
  <li class="nav-item {{ $route=='laporan.keluar' ? 'active' : '' }}"><a href="{{ route('laporan.keluar') }}"><i class="fas fa-file-export"></i><p>Laporan Barang Keluar</p></a></li>
@else
  <li class="nav-item {{ $route=='dashboard' ? 'active' : '' }}"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i><p>Dashboard</p></a></li>
@endif
