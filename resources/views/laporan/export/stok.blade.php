<center>
  <h4>{{ $fStok == 'Minimum' ? 'LAPORAN STOK BARANG YANG MENCAPAI BATAS MINIMUM' : 'LAPORAN STOK SELURUH BARANG' }}</h4>
</center>
<table border="1">
  <thead>
    <tr style="background-color:#231a98;color:#fff">
      <th height="30" align="center" vertical="center">No.</th>
      <th height="30" align="center" vertical="center">ID Barang</th>
      <th height="30" align="center" vertical="center">Nama Barang</th>
      <th height="30" align="center" vertical="center">Jenis Barang</th>
      <th height="30" align="center" vertical="center">Stok</th>
      <th height="30" align="center" vertical="center">Satuan</th>
    </tr>
  </thead>
  <tbody>
    @foreach($rows as $i => $d)
      <tr>
        <td width="70" align="center">{{ $i + 1 }}</td>
        <td width="120" align="center">{{ $d->id_barang }}</td>
        <td width="300">{{ $d->nama_barang }}</td>
        <td width="200">{{ $d->jenisRel->nama_jenis ?? '-' }}</td>
        @if($fStok == 'Seluruh' && $d->stok <= $d->stok_minimum)
          <td style="background-color:#ffad46;color:#fff" width="100" align="right">{{ $d->stok }}</td>
        @else
          <td width="100" align="right">{{ $d->stok }}</td>
        @endif
        <td width="150">{{ $d->satuanRel->nama_satuan ?? '-' }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
<br>
<div style="text-align:right; margin-top:20px;">
  {{ hari_indo(date('Y-m-d')) . ', ' . tanggal_indo(date('Y-m-d')) }}
</div>
