<center>
  <h4>
    LAPORAN DATA BARANG KELUAR<br>
    Tanggal {{ $fAwal }} s.d {{ $fAkhir }}
  </h4>
</center>
<table border="1">
  <thead>
    <tr style="background-color:#231a98;color:#fff">
      <th height="45" align="center" vertical="center">No.</th>
      <th height="45" align="center" vertical="center">ID Transaksi</th>
      <th height="45" align="center" vertical="center">Tanggal</th>
      <th height="45" align="center" vertical="center">Barang</th>
      <th height="45" align="center" vertical="center">Jumlah Keluar</th>
      <th height="45" align="center" vertical="center">Satuan</th>
    </tr>
  </thead>
  <tbody>
    @foreach($rows as $i => $d)
      <tr>
        <td width="70" align="center">{{ $i + 1 }}</td>
        <td width="150" align="center">{{ $d->id_transaksi }}</td>
        <td width="130" align="center">{{ date('d-m-Y', strtotime($d->tanggal)) }}</td>
        <td width="300">{{ $d->barang }} - {{ $d->barangRel->nama_barang ?? '-' }}</td>
        <td width="130" align="right">{{ number_format($d->jumlah, 0, '', '.') }}</td>
        <td width="130">{{ $d->barangRel->satuanRel->nama_satuan ?? '-' }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
<br>
<div style="text-align:right">
  {{ hari_indo(date('Y-m-d')) . ', ' . tanggal_indo(date('Y-m-d')) }}
</div>
