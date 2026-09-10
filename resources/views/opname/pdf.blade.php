<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Laporan Stock Opname</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .table { border-collapse: collapse; width: 100%; }
    .table th, .table td { border: 1px solid #000; padding: 5px; }
    .bg-secondary { background-color: #231a98; color: #fff; }
    .mt-4 { margin-top: 1rem; }
    .mt-5 { margin-top: 1.5rem; }
    img { object-fit: contain; }
  </style>
</head>
<body>
  <div class="text-center">
    <h2>LAPORAN STOCK OPNAME</h2>
    <span>
      @if($fAwal && $fAkhir)
        Periode: {{ tanggal_indo($fAwal) }} s/d {{ tanggal_indo($fAkhir) }}
      @else
        Seluruh Periode
      @endif
    </span>
  </div>
  <hr>
  <div class="mt-4">
    <table class="table">
      <thead class="bg-secondary text-center">
        <tr>
          <th>No.</th>
          <th>Tanggal</th>
          <th>Nama Barang</th>
          <th>Foto</th>
          <th>Serial / ID</th>
          <th>Stok Sistem</th>
          <th>Stok Fisik</th>
          <th>Selisih</th>
          <th>Status</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rows as $i => $d)
        @php $selisih = $d->stok_fisik - $d->stok_sistem; @endphp
        <tr>
          <td class="text-center">{{ $i + 1 }}</td>
          <td class="text-center">{{ hari_indo($d->tanggal_opname) . ', ' . date('d-m-Y', strtotime($d->tanggal_opname)) }}</td>
          <td>{{ $d->barang->nama_barang ?? '-' }}</td>
          <td class="text-center">
            @if(!empty($d->foto_base64))
              <img src="{{ $d->foto_base64 }}" width="50" height="50">
            @else
              <span style="color:#888;">-</span>
            @endif
          </td>
          <td class="text-center">{{ !empty($d->barang->serial_number) ? $d->barang->serial_number : 'ID: ' . $d->id_barang }}</td>
          <td class="text-center">{{ number_format($d->stok_sistem, 0, '', '.') }}</td>
          <td class="text-center">{{ number_format($d->stok_fisik, 0, '', '.') }}</td>
          <td class="text-center">{{ number_format($selisih, 0, '', '.') }}</td>
          <td class="text-center">{{ $d->status ?? '-' }}</td>
          <td>{{ !empty($d->keterangan) ? $d->keterangan : '-' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="text-right mt-5">{{ hari_indo(date('Y-m-d')) . ', ' . tanggal_indo(date('Y-m-d')) }}</div>
</body>
</html>
