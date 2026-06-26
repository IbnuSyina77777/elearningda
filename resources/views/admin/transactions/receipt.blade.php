<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi {{ $transaction->reference_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 30px; }
        .receipt-box { border: 2px dashed #ccc; padding: 30px; border-radius: 8px; position: relative; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 24px; letter-spacing: 2px; color: #111; }
        .header p { margin: 5px 0 0; font-size: 14px; color: #666; }
        .content-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .content-table td { padding: 10px 0; border-bottom: 1px solid #eee; }
        .content-table td.label { width: 35%; font-weight: bold; color: #555; }
        .content-table td.value { width: 65%; font-weight: 600; color: #111; }
        .amount-box { background: #f8f9fa; border: 1px solid #e9ecef; padding: 20px; text-align: center; border-radius: 8px; margin-bottom: 30px; }
        .amount-box h2 { margin: 0; font-size: 28px; color: #2c3e50; }
        .amount-box p { margin: 5px 0 0; color: #6c757d; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }
        .footer { display: table; width: 100%; margin-top: 40px; }
        .footer-left { display: table-cell; width: 50%; font-size: 12px; color: #666; }
        .footer-right { display: table-cell; width: 50%; text-align: right; }
        .stamp { font-size: 18px; font-weight: bold; color: #28a745; text-transform: uppercase; border: 3px solid #28a745; padding: 10px 20px; display: inline-block; border-radius: 5px; transform: rotate(-5deg); margin-top: 10px; }
    </style>
</head>
<body>

    @php
        $isLunas = $transaction->bill->status === 'paid';
        $remaining = $transaction->bill->amount - $transaction->bill->total_paid;
    @endphp

    <div class="receipt-box">
        <div class="header">
            <h1>KWITANSI PEMBAYARAN</h1>
            <p>Bukti Pembayaran Resmi</p>
        </div>

        <div class="amount-box">
            <h2>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</h2>
            <p>{{ $isLunas ? 'Telah Dibayar Lunas' : 'Pembayaran Cicilan' }}</p>
        </div>

        <table class="content-table">
            <tr>
                <td class="label">No. Referensi</td>
                <td class="value">{{ $transaction->reference_number }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Pembayaran</td>
                <td class="value">{{ $transaction->paid_at ? \Carbon\Carbon::parse($transaction->paid_at)->format('d F Y, H:i') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Diterima Dari (Siswa)</td>
                <td class="value">{{ $transaction->bill->student->name ?? '-' }} (Kelas: {{ $transaction->bill->student->classroom->name ?? '-' }})</td>
            </tr>
            <tr>
                <td class="label">NIS / NISN</td>
                <td class="value">{{ $transaction->bill->student->nis ?? '-' }} / {{ $transaction->bill->student->nisn ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Untuk Pembayaran</td>
                <td class="value">{{ $transaction->bill->paymentCategory->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Total Tagihan</td>
                <td class="value">Rp {{ number_format($transaction->bill->amount, 0, ',', '.') }}</td>
            </tr>
            @if(!$isLunas)
            <tr>
                <td class="label">Sisa Tagihan</td>
                <td class="value" style="color: #e53e3e;">Rp {{ number_format($remaining, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Metode Pembayaran</td>
                <td class="value">{{ strtoupper($transaction->payment_type) }}</td>
            </tr>
        </table>

        <div class="footer">
            <div class="footer-left">
                Kwitansi ini adalah bukti pembayaran yang sah.<br>
                Simpan baik-baik sebagai dokumen referensi.
            </div>
            <div class="footer-right">
                @if($isLunas)
                    <div class="stamp">LUNAS</div>
                @else
                    <div class="stamp" style="color: #f59e0b; border-color: #f59e0b;">CICILAN</div>
                @endif
            </div>
        </div>
    </div>

</body>
</html>
