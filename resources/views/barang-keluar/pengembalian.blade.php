@extends('layouts.app')
@section('title', 'Pengembalian Barang')

@section('content')
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-4">
    <div class="page-header text-white">
      <h4 class="page-title text-white"><i class="fas fa-undo mr-2"></i> Pengembalian Barang</h4>
      <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('barang-keluar.index') }}" class="text-white">Barang Keluar</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Pengembalian</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Entri Data Pengembalian Barang</div>
    </div>
    <form action="{{ $preselect ? route('barang-keluar.pengembalian.store', $preselect) : route('barang-keluar.pengembalian.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>ID Transaksi <span class="text-danger">*</span></label>
              <select class="form-control" name="id_transaksi" required {{ $preselect ? 'disabled' : '' }}>
                <option value="">-- Pilih ID Transaksi --</option>
                @forelse($list as $row)
                  <option value="{{ $row->id_transaksi }}" {{ (string) $preselect === (string) $row->id_transaksi ? 'selected' : '' }}>
                    {{ $row->id_transaksi }} - {{ $row->barangRel->nama_barang ?? '-' }}
                  </option>
                @empty
                  <option value="" disabled>Tidak ada transaksi yang perlu dikembalikan</option>
                @endforelse
              </select>
              @if($preselect)
                <input type="hidden" name="id_transaksi" value="{{ $preselect }}">
              @endif
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Tanggal Pengembalian <span class="text-danger">*</span></label>
              <input type="date" class="form-control" name="tanggal_pengembalian"
                     value="{{ old('tanggal_pengembalian', date('Y-m-d')) }}"
                     max="{{ date('Y-m-d') }}" required>
              <small class="text-muted">Tanggal tidak boleh lebih dari hari ini</small>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Kondisi Barang <span class="text-danger">*</span></label>
              <select class="form-control" name="kondisi" required>
                <option value="">-- Pilih Kondisi --</option>
                <option value="Baik" {{ old('kondisi') === 'Baik' ? 'selected' : '' }}>Baik</option>
                <option value="Rusak" {{ old('kondisi') === 'Rusak' ? 'selected' : '' }}>Rusak</option>
                <option value="Hilang" {{ old('kondisi') === 'Hilang' ? 'selected' : '' }}>Hilang</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Upload Foto Barang <span class="text-danger">*</span></label>
              <input type="file" class="form-control" name="foto"
                     accept="image/jpeg,image/jpg,image/png"
                     capture="environment" required>
              <small class="text-muted">Format: JPG, JPEG, PNG (maks. 3MB)</small>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>Catatan</label>
          <textarea class="form-control" name="catatan" rows="3"
                    placeholder="Tambahkan catatan jika ada kerusakan atau hal lain yang perlu dicatat...">{{ old('catatan') }}</textarea>
        </div>
      </div>
      <div class="card-action">
        <button type="submit" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">Simpan</button>
        <a href="{{ route('barang-keluar.index') }}" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const kondisiSelect = document.querySelector('select[name="kondisi"]');
  const catatanTextarea = document.querySelector('textarea[name="catatan"]');

  kondisiSelect.addEventListener('change', function() {
    if (this.value === 'Rusak' || this.value === 'Hilang') {
      catatanTextarea.setAttribute('required', 'required');
      catatanTextarea.placeholder = 'Wajib isi catatan untuk kondisi ' + this.value.toLowerCase();
    } else {
      catatanTextarea.removeAttribute('required');
      catatanTextarea.placeholder = 'Tambahkan catatan jika ada kerusakan atau hal lain yang perlu dicatat...';
    }
  });

  const fileInput = document.querySelector('input[name="foto"]');
  fileInput.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
      const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
      if (!allowedTypes.includes(file.type)) {
        alert('Format file tidak didukung! Gunakan JPG, JPEG, atau PNG');
        this.value = '';
      } else if (file.size > 3 * 1024 * 1024) {
        alert('Ukuran file terlalu besar! Maksimum 3MB');
        this.value = '';
      }
    }
  });
});
</script>
@endpush
