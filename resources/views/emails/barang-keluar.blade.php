<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Notifikasi Barang {{ $d['jenis'] ?? '' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background-color: white; border-radius: 8px; }
        .header { background-color: #667eea; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 30px; }
        .transaction-info { background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0; }
        .info-row { margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee; }
        .info-label { font-weight: bold; color: #333; }
        .info-value { color: #666; }
        .status-badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; background-color: #fff3cd; color: #856404; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; border-radius: 0 0 8px 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sistem Gudang Material</h1>
            <p>Notifikasi {{ ($d['jenis'] ?? '') === 'Pinjam' ? 'Peminjaman' : 'Pengeluaran' }} Barang</p>
        </div>

        <div class="content">
            <h2>Halo, {{ $d['user_name'] ?? '' }}!</h2>
            <p>Transaksi barang {{ strtolower($d['jenis'] ?? '') }} Anda telah berhasil dibuat dan sedang menunggu persetujuan.</p>

            <div class="transaction-info">
                <h3>Detail Transaksi</h3>
                <div class="info-row">
                    <span class="info-label">ID Transaksi:</span>
                    <span class="info-value"><strong>{{ $d['id_transaksi'] ?? '' }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal:</span>
                    <span class="info-value">{{ isset($d['tanggal']) ? date('d F Y', strtotime($d['tanggal'])) : '' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Barang:</span>
                    <span class="info-value">{{ $d['nama_barang'] ?? '' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jumlah:</span>
                    <span class="info-value">{{ isset($d['jumlah']) ? number_format($d['jumlah']) : '' }} {{ $d['satuan'] ?? '' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jenis:</span>
                    <span class="info-value">{{ $d['jenis'] ?? '' }}</span>
                </div>
                @if(!empty($d['serial_number']))
                <div class="info-row">
                    <span class="info-label">Serial Number:</span>
                    <span class="info-value">{{ $d['serial_number'] }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value"><span class="status-badge">Menunggu Persetujuan</span></span>
                </div>
            </div>

            <p><strong>Catatan:</strong> Transaksi Anda akan diproses oleh admin gudang. Anda akan menerima notifikasi lanjutan setelah transaksi disetujui atau ditolak.</p>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis dari Sistem Gudang Material.</p>
            <p>Jika Anda memiliki pertanyaan, silakan hubungi admin gudang.</p>
            <p>&copy; {{ date('Y') }} Sistem Gudang Material. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
