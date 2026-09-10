<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Laporan Data Barang Masuk</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .table { border-collapse: collapse; width: 100%; }
    .table th, .table td { border: 1px solid #000; padding: 5px; }
    .bg-secondary { background-color: #6c757d; color: #fff; }
    .mt-4 { margin-top: 1rem; }
    .mt-5 { margin-top: 1.5rem; }
    img { object-fit: contain; }
  </style>
</head>
<body>
  <div class="text-center">
    <h2>LAPORAN DATA BARANG MASUK</h2>
    <span>Tanggal {{ $fAwal }} s.d. {{ $fAkhir }}</span>
  </div>
  <hr>
  <div class="mt-4">
    <table class="table">
      <thead class="bg-secondary text-center">
        <tr>
          <th>No.</th>
          <th>ID Transaksi</th>
          <th>Tanggal (Hari)</th>
          <th>Barang</th>
          <th>Foto</th>
          <th>Jumlah Barang Masuk</th>
          <th>Satuan</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rows as $i => $d)
        <tr>
          <td class="text-center">{{ $i + 1 }}</td>
          <td class="text-center">{{ $d->id_transaksi }}</td>
          <td class="text-center">{{ hari_indo($d->tanggal) . ', ' . date('d-m-Y', strtotime($d->tanggal)) }}</td>
          <td>{{ $d->barang }} - {{ $d->barangRel->nama_barang ?? '-' }}</td>
          <td class="text-center">
            @if(!empty($d->foto_base64))
              <img src="{{ $d->foto_base64 }}" width="50" height="50">
            @else
              <span style="color:#888;">-</span>
            @endif
          </td>
          <td class="text-right">{{ number_format($d->jumlah, 0, '', '.') }}</td>
          <td class="text-center">{{ $d->barangRel->satuanRel->nama_satuan ?? '-' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="text-right mt-5">{{ hari_indo(date('Y-m-d')) . ', ' . tanggal_indo(date('Y-m-d')) }}</div>
</body>
</html>
