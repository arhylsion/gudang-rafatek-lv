<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>{{ $fStok == 'Minimum' ? 'Laporan Stok Barang Minimum' : 'Laporan Stok Barang Seluruh' }}</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
    h1 { font-size: 18px; margin-bottom: 10px; }
    table { border-collapse: collapse; width: 100%; }
    table, th, td { border: 1px solid #000; }
    th, td { padding: 6px; }
    th { background: #f2f2f2; text-align: center; }
    td.text-center { text-align: center; }
    td.text-right { text-align: right; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .mt-5 { margin-top: 40px; }
  </style>
</head>
<body>
  <div class="text-center">
    <h1>{{ $fStok == 'Minimum' ? 'LAPORAN STOK BARANG YANG MENCAPAI BATAS MINIMUM' : 'LAPORAN STOK BARANG SELURUH' }}</h1>
  </div>
  <hr>
  <div>
    <table>
      <thead>
        <tr>
          <th>No.</th>
          <th>ID Barang</th>
          <th>Nama Barang</th>
          <th>Foto Barang</th>
          <th>Jenis Barang</th>
          <th>Stok</th>
          <th>Satuan</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rows as $i => $d)
        <tr>
          <td class="text-center">{{ $i + 1 }}</td>
          <td class="text-center">{{ $d->id_barang }}</td>
          <td>{{ $d->nama_barang }}</td>
          <td class="text-center">
            @if(!empty($d->foto_base64))
              <img src="{{ $d->foto_base64 }}" width="60" height="60">
            @else
              <span>Tidak ada</span>
            @endif
          </td>
          <td>{{ $d->jenisRel->nama_jenis ?? '-' }}</td>
          <td class="text-right">{{ $d->stok }}</td>
          <td>{{ $d->satuanRel->nama_satuan ?? '-' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="text-right mt-5">{{ hari_indo(date('Y-m-d')) . ', ' . tanggal_indo(date('Y-m-d')) }}</div>
</body>
</html>
