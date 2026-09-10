<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Update Status Transaksi - {{ $d['status'] ?? '' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background-color: white; border-radius: 8px; }
        .header { background-color: #667eea; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 30px; }
        .status-banner { text-align: center; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .status-approved { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-rejected { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-other { background-color: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .transaction-info { background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0; }
        .info-row { margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee; }
        .info-label { font-weight: bold; color: #333; }
        .info-value { color: #666; }
        .status-badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; color: white; background-color: {{ ($d['status'] ?? '') === 'Disetujui' ? '#4caf50' : ((($d['status'] ?? '') === 'Ditolak') ? '#f44336' : '#ff9800') }}; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; border-radius: 0 0 8px 8px; }
        .admin-note { background-color: #e9ecef; padding: 15px; border-radius: 6px; margin: 15px 0; border-left: 4px solid #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sistem Gudang Material</h1>
            <p>Update Status Transaksi</p>
        </div>

        <div class="content">
            <h2>Halo, {{ $d['user_name'] ?? '' }}!</h2>

            <div class="status-banner {{ ($d['status'] ?? '') === 'Disetujui' ? 'status-approved' : ((($d['status'] ?? '') === 'Ditolak') ? 'status-rejected' : 'status-other') }}">
                <h3>{{ ($d['status'] ?? '') === 'Disetujui' ? 'Transaksi Anda telah disetujui!' : ((($d['status'] ?? '') === 'Ditolak') ? 'Transaksi Anda ditolak.' : 'Status transaksi Anda telah diperbarui.') }}</h3>
            </div>

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
                    <span class="info-value"><span class="status-badge">{{ $d['status'] ?? '' }}</span></span>
                </div>
            </div>

            @if(!empty($d['admin_note']))
            <div class="admin-note">
                <strong>Catatan Admin:</strong><br>
                {!! nl2br(e($d['admin_note'])) !!}
            </div>
            @endif

            @if(($d['status'] ?? '') === 'Disetujui')
                <p><strong>Selamat!</strong> Transaksi Anda telah disetujui dan barang dapat diambil sesuai prosedur yang berlaku.</p>
            @elseif(($d['status'] ?? '') === 'Ditolak')
                <p>Mohon maaf, transaksi Anda tidak dapat disetujui. Silakan hubungi admin gudang untuk informasi lebih lanjut.</p>
            @else
                <p>Status transaksi Anda telah diperbarui. Silakan cek sistem untuk informasi lebih lanjut.</p>
            @endif
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis dari Sistem Gudang Material.</p>
            <p>Jika Anda memiliki pertanyaan, silakan hubungi admin gudang.</p>
            <p>&copy; {{ date('Y') }} Sistem Gudang Material. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
