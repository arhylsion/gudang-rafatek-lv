<center>
  <h3>LAPORAN REKAPITULASI STOK OPNAME</h3>
  <h4>
    @if($fAwal && $fAkhir)
      Periode: {{ tanggal_indo($fAwal) }} s.d. {{ tanggal_indo($fAkhir) }}
    @elseif($fAwal)
      Mulai Tanggal: {{ tanggal_indo($fAwal) }}
    @elseif($fAkhir)
      Sampai Tanggal: {{ tanggal_indo($fAkhir) }}
    @else
      Seluruh Periode
    @endif
  </h4>
</center>

<table border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse; font-family: Arial; font-size: 12px; width: 100%;">
  <thead>
    <tr style="background-color:#231a98; color:#fff; text-align:center;">
      <th>No.</th>
      <th>Tanggal</th>
      <th>Nama Barang</th>
      <th>Serial / ID</th>
      <th>Stok Sistem</th>
      <th>Stok Fisik</th>
      <th>Selisih</th>
      <th>Status</th>
      <th>Keterangan</th>
    </tr>
  </thead>
  <tbody>
    @foreach($rows as $i => $row)
    @php
      $selisih = $row->stok_fisik - $row->stok_sistem;
      $id = $row->barang->serial_number ?? null;
      $id = $id ?: 'ID: ' . $row->id_barang;
      $tanggal = hari_indo($row->tanggal_opname) . ', ' . tanggal_indo($row->tanggal_opname);
    @endphp
    <tr>
      <td align="center">{{ $i + 1 }}</td>
      <td align="center">{{ $tanggal }}</td>
      <td>{{ $row->barang->nama_barang ?? '-' }}</td>
      <td align="center">{{ $id }}</td>
      <td align="center">{{ number_format($row->stok_sistem, 0, '', '.') }}</td>
      <td align="center">{{ number_format($row->stok_fisik, 0, '', '.') }}</td>
      <td align="center">{{ number_format($selisih, 0, '', '.') }}</td>
      <td align="center">{{ $row->status ?? '-' }}</td>
      <td>{{ !empty($row->keterangan) ? $row->keterangan : '-' }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<br>
<div style="text-align:right; font-size:12px; margin-top:10px;">
  {{ hari_indo(date('Y-m-d')) . ', ' . tanggal_indo(date('Y-m-d')) }}
</div>
