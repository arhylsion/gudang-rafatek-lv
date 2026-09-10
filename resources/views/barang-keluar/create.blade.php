@extends('layouts.app')
@section('title', $type === 'pinjam' ? 'Form Peminjaman Barang' : 'Form Barang Keluar')

@section('content')
<div id="pesan"></div>

<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-4">
    <div class="page-header text-white">
      <h4 class="page-title text-white"><i class="fas fa-sign-out-alt mr-2"></i> {{ $type === 'pinjam' ? 'Peminjaman Barang' : 'Barang Keluar' }}</h4>
      <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('barang-keluar.index') }}" class="text-white">Barang Keluar</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Entri</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">{{ $type === 'pinjam' ? 'Entri Data Peminjaman Barang' : 'Entri Data Barang Keluar' }}</div>
    </div>
    <form action="{{ route('barang-keluar.store') }}" method="POST" class="needs-validation" novalidate>
      @csrf
      <input type="hidden" name="jenis" value="{{ $jenis }}">
      <div class="card-body">
        {{-- Baris 1: ID Transaksi dan Tanggal --}}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>ID Transaksi <span class="text-danger">*</span></label>
              <input type="text" name="id_transaksi" class="form-control" value="{{ old('id_transaksi', $id_transaksi) }}" readonly>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>Tanggal <span class="text-danger">*</span></label>
              <input type="text" name="tanggal" class="form-control date-picker" autocomplete="off" value="{{ old('tanggal', date('d-m-Y')) }}" required>
              <div class="invalid-feedback">Tanggal tidak boleh kosong.</div>
            </div>
          </div>
        </div>

        <hr class="mt-3 mb-4">

        {{-- Baris 2: Barang dan Jumlah Keluar --}}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Barang <span class="text-danger">*</span></label>
              <select id="data_barang" name="barang" class="form-control chosen-select" autocomplete="off" required>
                <option selected disabled value="">-- Pilih --</option>
                @foreach($barangList as $b)
                  <option value="{{ $b->id_barang }}" {{ old('barang') == $b->id_barang ? 'selected' : '' }}>{{ $b->nama_barang }} - {{ $b->serial_number }}</option>
                @endforeach
              </select>
              <div class="invalid-feedback">Barang tidak boleh kosong.</div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>Jumlah Keluar <span class="text-danger">*</span></label>
              <input type="text" id="jumlah" name="jumlah" class="form-control" autocomplete="off" value="{{ old('jumlah') }}" onKeyPress="return goodchars(event,'0123456789',this)" required>
              <div class="invalid-feedback">Jumlah keluar tidak boleh kosong.</div>
            </div>
          </div>
        </div>

        {{-- Baris 3: Serial Number (readonly + dropdown modem bila tersedia) --}}
        <div class="row" id="serial_number_row" style="display:none;">
          <div class="col-md-12">
            <div class="form-group">
              <label>Serial Number</label>
              <input type="text" id="serial_number" name="serial_number" class="form-control" value="{{ old('serial_number') }}" readonly>
              <select id="serial_number_select" class="form-control mt-2" style="display:none;">
                <option value="">-- Pilih Serial Number --</option>
              </select>
              <small class="form-text text-muted">Untuk modem, pilih serial yang masih tersedia.</small>
            </div>
          </div>
        </div>

        {{-- Baris 4: Stok dan Sisa Stok --}}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Stok <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="text" id="data_stok" name="stok" class="form-control" readonly>
                <div id="data_satuan" class="input-group-append"></div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>Sisa Stok <span class="text-danger">*</span></label>
              <input type="text" id="sisa" name="sisa" class="form-control" readonly>
            </div>
          </div>
        </div>

        {{-- Baris 5: Lokasi Rak dan Keranjang (otomatis dari barang masuk, bisa diubah manual) --}}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Lokasi Rak <small class="text-muted">(Otomatis dari Barang Masuk)</small></label>
              <select name="id_rak" id="id_rak" class="form-control chosen-select" autocomplete="off" onchange="loadKeranjangKeluar()">
                <option selected disabled value="">-- Akan diisi otomatis --</option>
                @foreach($rakList as $r)
                  <option value="{{ $r->id_rak }}" data-lokasi="{{ $r->lokasi }}" {{ old('id_rak') == $r->id_rak ? 'selected' : '' }}>{{ $r->nama_rak }} - {{ $r->lokasi }}</option>
                @endforeach
              </select>
              <small class="form-text text-muted">Lokasi diambil dari transaksi barang masuk terakhir</small>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>Keranjang <small class="text-muted">(Otomatis dari Barang Masuk)</small></label>
              <select name="id_keranjang" id="id_keranjang" class="form-control chosen-select" autocomplete="off">
                <option selected disabled value="">-- Akan diisi otomatis --</option>
              </select>
              <small class="form-text text-muted">Keranjang diambil dari transaksi barang masuk terakhir</small>
            </div>
          </div>
        </div>

        {{-- Baris 6: Catatan --}}
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label>Catatan <span class="text-danger">*</span></label>
              <textarea name="catatan" class="form-control" rows="3" placeholder="Masukkan catatan transaksi" required>{{ old('catatan') }}</textarea>
              <div class="invalid-feedback">Catatan transaksi tidak boleh kosong.</div>
            </div>
          </div>
        </div>
      </div>
      <div class="card-action">
        <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
        <a href="{{ route('barang-keluar.index') }}" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
$(document).ready(function() {
  $('#data_barang').change(function() {
    var id_barang = $('#data_barang').val();

    $('#serial_number').val('');
    $('#serial_number_select').hide().html('<option value="">-- Pilih Serial Number --</option>');
    $('#serial_number_row').hide();

    $.ajax({
      type: "GET",
      url: "{{ route('barang-keluar.ajax-barang') }}",
      data: {id_barang: id_barang},
      dataType: "JSON",
      success: function(result) {
        $('#data_stok').val(result.stok);
        $('#data_satuan').html('<span class="input-group-text">' + (result.nama_satuan ?? '-') + '</span>');
        if (result.serial_number) {
          $('#serial_number').val(result.serial_number);
          $('#serial_number_row').show();
        }
        $('#jumlah').focus();
      }
    });

    // Daftar serial Available untuk modem (legacy get_serial_numbers.php)
    $.ajax({
      type: "GET",
      url: "{{ route('barang-keluar.ajax-serials') }}",
      data: {id_barang: id_barang},
      dataType: "JSON",
      success: function(result) {
        if (Array.isArray(result) && result.length > 0) {
          var opts = '<option value="">-- Pilih Serial Number --</option>';
          $.each(result, function(i, row) {
            opts += '<option value="' + row.serial_number + '">' + row.serial_number + '</option>';
          });
          $('#serial_number_select').html(opts).show();
          $('#serial_number_row').show();
        }
      }
    });

    // Auto-populate lokasi dari barang masuk terakhir
    $.ajax({
      type: "GET",
      url: "{{ route('barang-keluar.ajax-location') }}",
      data: {id_barang: id_barang},
      dataType: "JSON",
      success: function(result) {
        if (result.success && result.data) {
          $('#id_rak').val(result.data.id_rak);
          $('#id_rak').trigger('chosen:updated');

          if (result.data.id_keranjang) {
            loadKeranjangForRack(result.data.id_rak, result.data.id_keranjang);
          }

          $('#pesan').html('<div class="alert alert-notify alert-info alert-dismissible fade show" role="alert">' +
                          '<span data-notify="icon" class="fas fa-info"></span>' +
                          '<span data-notify="title" class="text-info">Info!</span> ' +
                          '<span data-notify="message">Lokasi otomatis diambil dari barang masuk: ' +
                          result.data.nama_rak + ' &rarr; ' + result.data.nama_keranjang + '</span>' +
                          '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                          '<span aria-hidden="true">&times;</span></button></div>');
        } else {
          $('#id_keranjang').html('<option selected disabled value="">-- Tidak ada data lokasi --</option>');
          $('#id_keranjang').trigger('chosen:updated');
          $('#pesan').html('<div class="alert alert-notify alert-warning alert-dismissible fade show" role="alert">' +
                          '<span data-notify="icon" class="fas fa-exclamation-triangle"></span>' +
                          '<span data-notify="title" class="text-warning">Peringatan!</span> ' +
                          '<span data-notify="message">Tidak ditemukan data lokasi dari barang masuk. Silakan pilih lokasi secara manual.</span>' +
                          '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                          '<span aria-hidden="true">&times;</span></button></div>');
        }
      },
      error: function() {
        $('#pesan').html('<div class="alert alert-notify alert-warning alert-dismissible fade show" role="alert">' +
                        '<span data-notify="icon" class="fas fa-exclamation-triangle"></span>' +
                        '<span data-notify="title" class="text-warning">Peringatan!</span> ' +
                        '<span data-notify="message">Gagal memuat data lokasi. Silakan pilih lokasi secara manual.</span>' +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span></button></div>');
      }
    });
  });

  $('#serial_number_select').change(function() {
    $('#serial_number').val($(this).val());
  });

  $('#jumlah').keyup(function() {
    var stok = $('#data_stok').val();
    var jumlah = $('#jumlah').val();

    if (stok == "") {
      $('#pesan').html('<div class="alert alert-notify alert-info alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-info"></span><span data-notify="title" class="text-info">Info!</span> <span data-notify="message">Silahkan isi data barang terlebih dahulu.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
      $('#jumlah').val('');
      var sisa_stok = "";
    }
    else if (jumlah == "") {
      var sisa_stok = "";
    }
    else if (jumlah == 0) {
      $('#pesan').html('<div class="alert alert-notify alert-warning alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-exclamation"></span><span data-notify="title" class="text-warning">Peringatan!</span> <span data-notify="message">Jumlah keluar tidak boleh 0 (nol).</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
      $('#jumlah').val('');
      var sisa_stok = "";
    }
    else if (eval(jumlah) > eval(stok)) {
      $('#pesan').html('<div class="alert alert-notify alert-warning alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-exclamation"></span><span data-notify="title" class="text-warning">Peringatan!</span> <span data-notify="message">Stok tidak memenuhi, kurangi jumlah keluar.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
      $('#jumlah').val('');
      var sisa_stok = "";
    }
    else {
      var sisa_stok = eval(stok) - eval(jumlah);
    }

    $('#sisa').val(sisa_stok);
  });
});

function loadKeranjangKeluar() {
  var idRak = $('#id_rak').val();
  if (!idRak) {
    refreshKeranjangSelect('<option selected disabled value="">-- Pilih Rak Terlebih Dahulu --</option>');
    return;
  }
  $.ajax({
    url: '{{ route('barang-keluar.ajax-location') }}',
    type: 'GET',
    data: { id_rak: idRak },
    dataType: 'json',
    success: function(response) {
      var options = '<option selected disabled value="">-- Pilih Keranjang --</option>';
      if (response.success && response.data.length > 0) {
        $.each(response.data, function(index, keranjang) {
          options += '<option value="' + keranjang.id_keranjang + '">' +
                    keranjang.nama_keranjang + ' (' + keranjang.kondisi + ')</option>';
        });
      } else {
        options += '<option disabled value="">-- Tidak ada keranjang tersedia --</option>';
      }
      refreshKeranjangSelect(options);
    },
    error: function() {
      refreshKeranjangSelect('<option selected disabled value="">-- Error loading data --</option>');
    }
  });
}

function loadKeranjangForRack(idRak, selectedKeranjangId) {
  if (!idRak) return;
  $.ajax({
    url: '{{ route('barang-keluar.ajax-location') }}',
    type: 'GET',
    data: { id_rak: idRak },
    dataType: 'json',
    success: function(response) {
      var options = '<option selected disabled value="">-- Pilih Keranjang --</option>';
      if (response.success && response.data.length > 0) {
        $.each(response.data, function(index, keranjang) {
          var selected = keranjang.id_keranjang == selectedKeranjangId ? 'selected' : '';
          options += '<option value="' + keranjang.id_keranjang + '" ' + selected + '>' +
                    keranjang.nama_keranjang + ' (' + keranjang.kondisi + ')</option>';
        });
      } else {
        options += '<option disabled value="">-- Tidak ada keranjang tersedia --</option>';
      }
      refreshKeranjangSelect(options);
    },
    error: function() {
      refreshKeranjangSelect('<option selected disabled value="">-- Error loading data --</option>');
    }
  });
}

function refreshKeranjangSelect(optionsHtml) {
  if ($('#id_keranjang').hasClass('chosen-select') && $.fn.chosen) { $('#id_keranjang').chosen('destroy'); }
  $('#id_keranjang').html(optionsHtml);
  if ($.fn.chosen) { $('#id_keranjang').addClass('chosen-select').chosen({ width: '100%' }); }
  else { $('#id_keranjang').trigger('chosen:updated'); }
}
</script>
@endpush
