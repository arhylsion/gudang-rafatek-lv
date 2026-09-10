@extends('layouts.app')
@section('title', 'Entri Barang Masuk')

@section('content')
<div id="pesan"></div>

<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-4">
    <div class="page-header text-white">
      <h4 class="page-title text-white"><i class="fas fa-sign-in-alt mr-2"></i> Barang Masuk</h4>
      <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="flaticon-home text-white"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('barang-masuk.index') }}" class="text-white">Barang Masuk</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a>Entri</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Entri Data Barang Masuk</div>
    </div>
    <form action="{{ route('barang-masuk.store') }}" method="POST" class="needs-validation" novalidate>
      @csrf
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

        {{-- Baris 2: Barang dan Jumlah Masuk --}}
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
              <label>Jumlah Masuk <span class="text-danger">*</span></label>
              <input type="text" id="jumlah" name="jumlah" class="form-control" autocomplete="off" value="{{ old('jumlah') }}" onKeyPress="return goodchars(event,'0123456789',this)" required>
              <div class="invalid-feedback">Jumlah masuk tidak boleh kosong.</div>
            </div>
          </div>
        </div>

        {{-- Baris 3: Stok dan Total Stok --}}
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
              <label>Total Stok <span class="text-danger">*</span></label>
              <input type="text" id="total" name="total" class="form-control" readonly>
            </div>
          </div>
        </div>

        {{-- Baris 4: Serial Number --}}
        <div class="row">
          <div class="col-md-12">
            <div class="form-group" id="serial_number_group" style="display: none;">
              <label>Serial Number</label>
              <input type="text" id="serial_number" name="serial_number" class="form-control" value="{{ old('serial_number') }}" readonly>
              <small class="form-text text-muted">Serial number otomatis terisi dari data barang.</small>
            </div>
          </div>
        </div>

        {{-- Baris 5: Lokasi Rak dan Keranjang --}}
        <div class="row">
          <div class="col-md-6">
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
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>Keranjang</label>
              <select name="id_keranjang" id="id_keranjang" class="form-control chosen-select" autocomplete="off">
                <option selected disabled value="">-- Pilih Rak Terlebih Dahulu --</option>
              </select>
              <small class="form-text text-muted">Pilih keranjang dalam rak yang dipilih</small>
            </div>
          </div>
        </div>
      </div>
      <div class="card-action">
        <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
        <a href="{{ route('barang-masuk.index') }}" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
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

    $.ajax({
      type: "GET",
      url: "{{ route('barang-masuk.ajax-barang') }}",
      data: {id_barang: id_barang},
      dataType: "JSON",
      success: function(result) {
        $('#data_stok').val(result.stok);
        $('#data_satuan').html('<span class="input-group-text">' + result.nama_satuan + '</span>');

        if (result.serial_number) {
          $('#serial_number').val(result.serial_number);
          $('#serial_number_group').slideDown();
          $('#serial_number').attr('required', true);
          $('#serial_number').attr('readonly', true);
        } else {
          $('#serial_number_group').slideUp();
          $('#serial_number').attr('required', false);
          $('#serial_number').val('');
          $('#serial_number').removeAttr('readonly');
        }

        if (result.needs_serial || (result.nama_barang && result.nama_barang.toLowerCase().includes('modem')) || result.is_modem) {
          if (!result.serial_number) {
            $('#serial_number_group').slideDown();
            $('#serial_number').attr('required', true);
            $('#serial_number').removeAttr('readonly');
          } else {
            $('#serial_number').val(result.serial_number);
            $('#serial_number_group').slideDown();
            $('#serial_number').attr('required', true);
            $('#serial_number').attr('readonly', true);
          }

          $('#jumlah').val('1');
          $('#jumlah').attr('readonly', true);
          $('#jumlah').removeClass('is-invalid');
        } else {
          $('#jumlah').removeAttr('readonly');
          $('#jumlah').val('');
          $('#serial_number_group').slideUp();
          $('#serial_number').attr('required', false);
          $('#serial_number').val('');
        }

        autoFillRackAndBasket(id_barang);

        $('#jumlah').focus();
      }
    });
  });

  $('#jumlah').keyup(function() {
    var stok = $('#data_stok').val();
    var jumlah = $('#jumlah').val();

    if (stok == "") {
      $('#pesan').html('<div class="alert alert-notify alert-info alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-info"></span><span data-notify="title" class="text-info">Info!</span> <span data-notify="message">Silahkan isi data barang terlebih dahulu.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
      $('#jumlah').val('');
      var total_stok = "";
    }
    else if (jumlah == "") {
      var total_stok = "";
    }
    else if (jumlah == 0) {
      $('#pesan').html('<div class="alert alert-notify alert-warning alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-exclamation"></span><span data-notify="title" class="text-warning">Peringatan!</span> <span data-notify="message">Jumlah masuk tidak boleh 0 (nol).</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
      $('#jumlah').val('');
      var total_stok = "";
    }
    else {
      var total_stok = eval(stok) + eval(jumlah);
    }

    $('#total').val(total_stok);
  });
});

function loadKeranjang() {
  var idRak = $('#id_rak').val();

  if (idRak) {
    $.ajax({
      url: '{{ route('barang-masuk.ajax-location') }}',
      type: 'GET',
      data: { id_rak: idRak },
      dataType: 'json',
      success: function(response) {
        var options = '<option selected disabled value="">-- Pilih Keranjang --</option>';

        if (response.success && response.data.length > 0) {
          $.each(response.data, function(index, keranjang) {
            options += '<option value="' + keranjang.id_keranjang + '">' +
                      keranjang.nama_keranjang + ' (' + keranjang.kondisi + ')' + '</option>';
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
  } else {
    refreshKeranjangSelect('<option selected disabled value="">-- Pilih Rak Terlebih Dahulu --</option>');
  }
}

function autoFillRackAndBasket(id_barang) {
  $.ajax({
    type: "GET",
    url: "{{ route('barang-masuk.ajax-location') }}",
    data: {id_barang: id_barang},
    dataType: "JSON",
    success: function(result) {
      if (result.success && result.data) {
        if (result.data.id_rak) {
          $('#id_rak').val(result.data.id_rak);
          $('#id_rak').trigger('chosen:updated');

          if (result.data.id_keranjang) {
            loadKeranjangForRack(result.data.id_rak, result.data.id_keranjang);
          }
        }
      }
    }
  });
}

function loadKeranjangForRack(idRak, selectedKeranjangId) {
  if (idRak) {
    $.ajax({
      url: '{{ route('barang-masuk.ajax-location') }}',
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
}

function refreshKeranjangSelect(optionsHtml) {
  if ($('#id_keranjang').hasClass('chosen-select') && $.fn.chosen) { $('#id_keranjang').chosen('destroy'); }
  $('#id_keranjang').html(optionsHtml);
  if ($.fn.chosen) { $('#id_keranjang').addClass('chosen-select').chosen({ width: '100%' }); }
  else { $('#id_keranjang').trigger('chosen:updated'); }
}
</script>
@endpush
