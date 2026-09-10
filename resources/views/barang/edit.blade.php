@extends('layouts.app')
@section('title', 'Ubah Barang')

@section('content')
<div id="pesan"></div>

<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-4">
    <div class="page-header text-white">
      <h4 class="page-title text-white"><i class="fas fa-clone mr-2"></i> Barang</h4>
      <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('barang.index') }}" class="text-white">Barang</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Ubah</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Ubah Data Barang</div>
    </div>
    <form action="{{ route('barang.update', $barang->id_barang) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
      @csrf
      @method('PUT')
      <div class="card-body">
        <div class="row">
          <div class="col-md-7">
            <div class="form-group">
              <label>ID Barang <span class="text-danger">*</span></label>
              <input type="text" name="id_barang" class="form-control" value="{{ $barang->id_barang }}" readonly>
            </div>

            <div class="form-group">
              <label>Nama Barang <span class="text-danger">*</span></label>
              <input type="text" name="nama_barang" class="form-control" autocomplete="off" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
              <div class="invalid-feedback">Nama barang tidak boleh kosong.</div>
            </div>

            <div class="form-group">
              <label>Jenis Barang <span class="text-danger">*</span></label>
              <select name="jenis" class="form-control chosen-select" autocomplete="off" required>
                <option value="{{ $barang->jenis }}">{{ $barang->jenisRel->nama_jenis ?? '-' }}</option>
                <option disabled value="">-- Pilih --</option>
                @foreach($jenisList as $j)
                  @if($j->id_jenis != $barang->jenis)
                    <option value="{{ $j->id_jenis }}">{{ $j->nama_jenis }}</option>
                  @endif
                @endforeach
              </select>
              <div class="invalid-feedback">Jenis Barang tidak boleh kosong.</div>
            </div>

            <div class="form-group">
              <label>Stok Minimum <span class="text-danger">*</span></label>
              <input type="text" name="stok_minimum" class="form-control" autocomplete="off" onKeyPress="return goodchars(event,'0123456789',this)" value="{{ old('stok_minimum', $barang->stok_minimum) }}" required>
              <div class="invalid-feedback">Stok minimum tidak boleh kosong.</div>
            </div>

            @if($barang->serial_number)
            <div class="form-group">
              <label>Serial Number</label>
              <input type="text" class="form-control" value="{{ $barang->serial_number }}" readonly>
            </div>
            @endif

            <div class="form-group">
              <label>Satuan <span class="text-danger">*</span></label>
              <select name="satuan" class="form-control chosen-select" autocomplete="off" required>
                <option value="{{ $barang->satuan }}">{{ $barang->satuanRel->nama_satuan ?? '-' }}</option>
                <option disabled value="">-- Pilih --</option>
                @foreach($satuanList as $s)
                  @if($s->id_satuan != $barang->satuan)
                    <option value="{{ $s->id_satuan }}">{{ $s->nama_satuan }}</option>
                  @endif
                @endforeach
              </select>
              <div class="invalid-feedback">Satuan tidak boleh kosong.</div>
            </div>
          </div>
          <div class="col-md-5 ml-auto">
            <div class="form-group">
              <label>Lokasi Rak</label>
              <select name="id_rak" id="id_rak" class="form-control chosen-select" autocomplete="off" onchange="loadKeranjang()">
                @if($barang->id_rak)
                  <option value="{{ $barang->id_rak }}">{{ $barang->rak->nama_rak ?? '' }} - {{ $barang->rak->lokasi ?? '' }}</option>
                @endif
                <option selected disabled value="">-- Pilih Rak --</option>
                @foreach($rakList as $r)
                  @if($r->id_rak != $barang->id_rak)
                    <option value="{{ $r->id_rak }}" data-lokasi="{{ $r->lokasi }}">{{ $r->nama_rak }} - {{ $r->lokasi }}</option>
                  @endif
                @endforeach
              </select>
              <small class="form-text text-muted">Pilih rak untuk penempatan barang</small>
            </div>

            <div class="form-group">
              <label>Keranjang</label>
              <select name="id_keranjang" id="id_keranjang" class="form-control chosen-select" autocomplete="off">
                @if($barang->id_keranjang)
                  <option value="{{ $barang->id_keranjang }}">{{ $barang->keranjang->nama_keranjang ?? '' }} ({{ $barang->keranjang->kondisi ?? '' }})</option>
                @endif
                <option selected disabled value="">-- Pilih Rak Terlebih Dahulu --</option>
              </select>
              <small class="form-text text-muted">Pilih keranjang dalam rak yang dipilih</small>
            </div>

            <div class="form-group">
              <label>Foto Barang</label>
              <input type="file" id="foto" name="foto" class="form-control" autocomplete="off">
              <div class="card mt-3 mb-3">
                <div class="card-body text-center">
                  @if($barang->foto && file_exists(public_path('images/'.$barang->foto)))
                    <img style="max-height:200px" src="{{ asset('images/'.$barang->foto) }}" class="img-fluid foto-preview" alt="Foto Barang">
                  @else
                    <img style="max-height:200px" src="{{ asset('images/no_image.png') }}" class="img-fluid foto-preview" alt="Foto Barang">
                  @endif
                </div>
              </div>
              <small class="form-text text-secondary">
                Keterangan : <br>
                - Tipe file yang bisa diunggah adalah *.jpg atau *.png. <br>
                - Ukuran file yang bisa diunggah maksimal 3 Mb.
              </small>
            </div>
          </div>
        </div>
      </div>
      <div class="card-action">
        <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
        <a href="{{ route('barang.index') }}" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
$(document).ready(function() {
  @if($barang->id_rak)
    loadKeranjang();
  @endif

  $('#foto').change(function() {
    var filePath = $('#foto').val();
    var fileSize = $('#foto')[0].files[0].size;
    var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
    if (!allowedExtensions.exec(filePath)) {
      $('#pesan').html('<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-times"></span><span data-notify="title" class="text-danger">Gagal!</span> <span data-notify="message">Tipe file tidak sesuai. Harap unggah file yang memiliki tipe *.jpg atau *.png.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
      $('#foto').val('');
      $('.foto-preview').attr('src', '{{ asset('images/no_image.png') }}');
      return false;
    } else if (fileSize > 3000000) {
      $('#pesan').html('<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-times"></span><span data-notify="title" class="text-danger">Gagal!</span> <span data-notify="message">Ukuran file lebih dari 3 Mb. Harap unggah file yang memiliki ukuran maksimal 3 Mb.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
      $('#foto').val('');
      $('.foto-preview').attr('src', '{{ asset('images/no_image.png') }}');
      return false;
    } else {
      var fileInput = document.getElementById('foto');
      if (fileInput.files && fileInput.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) { $('.foto-preview').attr('src', e.target.result); };
        reader.readAsDataURL(fileInput.files[0]);
      }
    }
  });
});

function loadKeranjang() {
  var idRak = $('#id_rak').val();
  var currentKeranjang = @json($barang->id_keranjang);
  if (idRak) {
    $.ajax({
      url: '{{ route('barang.getKeranjang') }}',
      type: 'GET',
      data: { id_rak: idRak },
      dataType: 'json',
      success: function(response) {
        var options = '';
        if (currentKeranjang && response.success) {
          var found = false;
          $.each(response.data, function(index, keranjang) {
            if (keranjang.id_keranjang == currentKeranjang) {
              options += '<option value="' + keranjang.id_keranjang + '" selected>' + keranjang.nama_keranjang + ' (' + keranjang.kondisi + ')</option>';
              found = true;
            }
          });
          if (!found) {
            options += '<option value="' + currentKeranjang + '" selected>{{ $barang->keranjang->nama_keranjang ?? '' }} (Current)</option>';
          }
        }
        options += '<option disabled value="">-- Pilih Keranjang --</option>';
        if (response.success && response.data.length > 0) {
          $.each(response.data, function(index, keranjang) {
            if (keranjang.id_keranjang != currentKeranjang) {
              options += '<option value="' + keranjang.id_keranjang + '">' + keranjang.nama_keranjang + ' (' + keranjang.kondisi + ')</option>';
            }
          });
        } else {
          options += '<option disabled value="">-- Tidak ada keranjang tersedia --</option>';
        }
        if ($('#id_keranjang').hasClass('chosen-select')) { $('#id_keranjang').chosen('destroy'); }
        $('#id_keranjang').html(options);
        $('#id_keranjang').addClass('chosen-select').chosen({ width: '100%', placeholder_text_single: '-- Pilih Keranjang --' });
      },
      error: function() {
        $('#pesan').html('<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-times"></span><span data-notify="title" class="text-danger">Error!</span> <span data-notify="message">Error loading keranjang data</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
      }
    });
  } else {
    var options = '<option selected disabled value="">-- Pilih Rak Terlebih Dahulu --</option>';
    if (currentKeranjang) {
      options = '<option value="' + currentKeranjang + '" selected>{{ $barang->keranjang->nama_keranjang ?? '' }} (Current)</option>' + options;
    }
    if ($('#id_keranjang').hasClass('chosen-select')) { $('#id_keranjang').chosen('destroy'); }
    $('#id_keranjang').html(options);
    $('#id_keranjang').addClass('chosen-select').chosen({ width: '100%', placeholder_text_single: '-- Pilih Rak Terlebih Dahulu --' });
  }
}
</script>
@endpush
