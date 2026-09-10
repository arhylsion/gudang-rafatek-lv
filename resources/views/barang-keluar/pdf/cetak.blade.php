<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td, th { padding: 8px; border: 1px solid #333; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .serial-row td { vertical-align: middle; }
        .sn-box { border: 1px solid #ccc; padding: 5px; text-align: center; font-family: monospace; background: #f9f9f9; }
    </style>
</head>
<body>
    @php
        $bulan = [1 => 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $hariIndo = ['Sunday' => 'Minggu','Monday' => 'Senin','Tuesday' => 'Selasa','Wednesday' => 'Rabu','Thursday' => 'Kamis','Friday' => 'Jumat','Saturday' => 'Sabtu'];
        $tgl = date('Y-m-d', strtotime($trx->tanggal));
        $split = explode('-', $tgl);
        $tanggalFormatted = intval($split[2]).' '.($bulan[intval($split[1])] ?? $split[1]).' '.$split[0];
        $hari = $hariIndo[date('l', strtotime($trx->tanggal))] ?? '';
        $now = date('Y-m-d');
        $nsplit = explode('-', $now);
        $tanggalCetak = intval($nsplit[2]).' '.($bulan[intval($nsplit[1])] ?? $nsplit[1]).' '.$nsplit[0];
    @endphp
    <div class="header">
        <h2>Laporan Transaksi Barang Keluar</h2>
        <hr style="border: 1px solid #333;">
    </div>

    <table>
        <tr><th width="25%">ID Transaksi</th><td>{{ $trx->id_transaksi }}</td></tr>
        <tr><th>Tanggal</th><td>{{ $hari }}, {{ $tanggalFormatted }}</td></tr>
        <tr><th>Nama Barang</th><td>{{ $trx->barangRel->nama_barang ?? '-' }}</td></tr>
        <tr class="serial-row">
            <th>Serial Number</th>
            <td>
                <strong>{{ $sn }}</strong><br>
                <div class="sn-box">{{ $sn }}</div>
            </td>
        </tr>
        <tr>
            <th>Foto Barang</th>
            <td>
                @if($fotoBase64)
                    <img src="{{ $fotoBase64 }}" width="100" height="100" style="object-fit:contain;">
                @else
                    <span style="color:#888">Tidak ada</span>
                @endif
            </td>
        </tr>
        <tr><th>Jumlah</th><td>{{ $trx->jumlah }} {{ $trx->barangRel->satuanRel->nama_satuan ?? '' }}</td></tr>
        <tr><th>Jenis</th><td>{{ $trx->jenis }}</td></tr>
        <tr><th>Status</th><td>{{ $trx->status }}</td></tr>
        @if($trx->tanggal_pengembalian)
        <tr><th>Tanggal Pengembalian</th><td>{{ date('d-m-Y', strtotime($trx->tanggal_pengembalian)) }}</td></tr>
        @endif
        @if($trx->kondisi)
        <tr><th>Kondisi</th><td>{{ $trx->kondisi }}</td></tr>
        @endif
        @if($fotoKembaliBase64)
        <tr><th>Foto Pengembalian</th><td><img src="{{ $fotoKembaliBase64 }}" width="100" height="100" style="object-fit:contain;"></td></tr>
        @endif
    </table>

    <div style="margin-top: 30px; text-align: right;">
        <p>Dicetak pada: {{ $tanggalCetak }}</p>
    </div>
</body>
</html>
