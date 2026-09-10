@extends('layouts.app')
@section('title', 'Entri Barang')

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
        <li class="nav-item"><a>Entri</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Entri Data Barang</div>
    </div>
    <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
      @csrf
      <div class="card-body">
        <div class="row">
          <div class="col-md-7">
            <div class="form-group">
              <label>ID Barang <span class="text-danger">*</span></label>
              <input type="text" name="id_barang" class="form-control" value="{{ old('id_barang', $id_barang) }}" readonly>
            </div>

            <div class="form-group">
              <label>Nama Barang <span class="text-danger">*</span></label>
              <input type="text" name="nama_barang" class="form-control" autocomplete="off" value="{{ old('nama_barang') }}" required>
              <div class="invalid-feedback">Nama barang tidak boleh kosong.</div>
            </div>

            <div class="form-group">
              <label>Jenis Barang <span class="text-danger">*</span></label>
              <select name="jenis" id="jenis_barang" class="form-control chosen-select" autocomplete="off" required>
                <option selected disabled value="">-- Pilih --</option>
                @foreach($jenisList as $j)
                  <option value="{{ $j->id_jenis }}" data-nama="{{ $j->nama_jenis }}" {{ old('jenis') == $j->id_jenis ? 'selected' : '' }}>{{ $j->nama_jenis }}</option>
                @endforeach
              </select>
              <div class="invalid-feedback">Jenis Barang tidak boleh kosong.</div>
            </div>

            <div class="form-group">
              <label>Stok Minimum <span class="text-danger">*</span></label>
              <input type="text" name="stok_minimum" class="form-control" autocomplete="off" value="{{ old('stok_minimum') }}" onKeyPress="return goodchars(event,'0123456789',this)" required>
              <div class="invalid-feedback">Stok minimum tidak boleh kosong.</div>
            </div>

            <div class="row" id="serial_number_section" style="display: none;">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Serial Number <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="text" id="serial_number" name="serial_number" class="form-control"
                           placeholder="Masukkan atau scan serial number barang" autocomplete="off" value="{{ old('serial_number') }}">
                  </div>
                  <small class="text-muted">Untuk modem: beberapa serial dipisahkan koma (contoh: SN001, SN002). Tersimpan ke inventory serial.</small>
                  <div class="invalid-feedback">Serial number tidak boleh kosong.</div>
                </div>
              </div>
            </div>

            <div class="row" id="serial_status" style="display: none;">
              <div class="col-md-12">
                <div id="serial_alert" class="alert alert-info">
                  <div class="row">
                    <div class="col-md-12">
                      <span id="serial_message"><i class="fas fa-info-circle mr-2"></i>Serial number telah diisi dari hasil scan barcode.</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label>Satuan <span class="text-danger">*</span></label>
              <select name="satuan" class="form-control chosen-select" autocomplete="off" required>
                <option selected disabled value="">-- Pilih --</option>
                @foreach($satuanList as $s)
                  <option value="{{ $s->id_satuan }}" {{ old('satuan') == $s->id_satuan ? 'selected' : '' }}>{{ $s->nama_satuan }}</option>
                @endforeach
              </select>
              <div class="invalid-feedback">Satuan tidak boleh kosong.</div>
            </div>

            <div class="form-group">
              <label>Stok <span class="text-danger">*</span></label>
              <input type="text" name="stok" class="form-control" autocomplete="off" value="{{ old('stok', 0) }}" onKeyPress="return goodchars(event,'0123456789',this)" required>
              <div class="invalid-feedback">Stok tidak boleh kosong.</div>
            </div>
          </div>
          <div class="col-md-5 ml-auto">
            <div class="form-group">
              <label>Lokasi Rak</label>
              <select name="id_rak" id="id_rak" class="form-control chosen-select" autocomplete="off" onchange="loadKeranjang()">
                <option selected disabled value="">-- Pilih Rak --</option>
                @foreach($rakList as $r)
                  <option value="{{ $r->id_rak }}" data-lokasi="{{ $r->lokasi }}" {{ old('id_rak') == $r->id_rak ? 'selected' : '' }}>{{ $r->nama_rak }} - {{ $r->lokasi }}</option>
                @endforeach
              </select>
              <small class="form-text text-muted">Pilih rak untuk penempatan barang</small>
            </div>

            <div class="form-group">
              <label>Keranjang</label>
              <select name="id_keranjang" id="id_keranjang" class="form-control chosen-select" autocomplete="off">
                <option selected disabled value="">-- Pilih Rak Terlebih Dahulu --</option>
              </select>
              <small class="form-text text-muted">Pilih keranjang dalam rak yang dipilih</small>
            </div>

            <div class="form-group">
              <label>Foto Barang</label>
              <input type="file" id="foto" name="foto" class="form-control" autocomplete="off">
              <div class="card mt-3 mb-3">
                <div class="card-body text-center">
                  <img style="max-height:200px" src="{{ asset('images/no_image.png') }}" class="img-fluid foto-preview" alt="Foto Barang">
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
  function toggleSerialNumberField() {
    var selectedOption = $('#jenis_barang option:selected');
    var jenisNama = selectedOption.data('nama');
    if (jenisNama && String(jenisNama).toUpperCase() === 'MODEM') {
      $('#serial_number_section').show();
      $('#serial_number').attr('required', true);
    } else {
      $('#serial_number_section').hide();
      $('#serial_status').hide();
      $('#serial_number').attr('required', false);
      $('#serial_number').val('');
    }
  }

  $('#jenis_barang').change(function() { toggleSerialNumberField(); });
  toggleSerialNumberField();

  $('#foto').change(function() {
    var filePath = $('#foto').val();
    var fileSize = $('#foto')[0].files[0].size;
    var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
    if (!allowedExtensions.exec(filePath)) {
      showAlert('danger', 'Tipe file tidak sesuai. Harap unggah file yang memiliki tipe *.jpg atau *.png.');
      $('#foto').val('');
      $('.foto-preview').attr('src', '{{ asset('images/no_image.png') }}');
      return false;
    } else if (fileSize > 3000000) {
      showAlert('danger', 'Ukuran file lebih dari 3 Mb. Harap unggah file yang memiliki ukuran maksimal 3 Mb.');
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

function showAlert(type, message, title) {
  const icons = { success: 'fas fa-check', danger: 'fas fa-times', warning: 'fas fa-exclamation-triangle', info: 'fas fa-info-circle' };
  const titles = { success: title || 'Berhasil!', danger: title || 'Gagal!', warning: title || 'Peringatan!', info: title || 'Informasi!' };
  $('#pesan').html(
    '<div class="alert alert-notify alert-' + type + ' alert-dismissible fade show" role="alert">' +
    '<span data-notify="icon" class="' + icons[type] + '"></span> ' +
    '<span data-notify="title" class="text-' + type + '">' + titles[type] + '</span> ' +
    '<span data-notify="message">' + message + '</span>' +
    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
  );
}

function loadKeranjang() {
  var idRak = $('#id_rak').val();
  if (idRak) {
    $.ajax({
      url: '{{ route('barang.getKeranjang') }}',
      type: 'GET',
      data: { id_rak: idRak },
      dataType: 'json',
      success: function(response) {
        var options = '<option selected disabled value="">-- Pilih Keranjang --</option>';
        if (response.success && response.data.length > 0) {
          $.each(response.data, function(index, keranjang) {
            options += '<option value="' + keranjang.id_keranjang + '">' + keranjang.nama_keranjang + ' (' + keranjang.kondisi + ')</option>';
          });
        } else {
          options += '<option disabled value="">-- Tidak ada keranjang tersedia --</option>';
        }
        if ($('#id_keranjang').hasClass('chosen-select')) { $('#id_keranjang').chosen('destroy'); }
        $('#id_keranjang').html(options);
        $('#id_keranjang').addClass('chosen-select').chosen({ width: '100%', placeholder_text_single: '-- Pilih Keranjang --' });
      },
      error: function() { showAlert('danger', 'Error loading keranjang data'); }
    });
  } else {
    if ($('#id_keranjang').hasClass('chosen-select')) { $('#id_keranjang').chosen('destroy'); }
    $('#id_keranjang').html('<option selected disabled value="">-- Pilih Rak Terlebih Dahulu --</option>');
    $('#id_keranjang').addClass('chosen-select').chosen({ width: '100%', placeholder_text_single: '-- Pilih Rak Terlebih Dahulu --' });
  }
}
</script>
@endpush
