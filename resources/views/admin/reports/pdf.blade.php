<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        @page { margin: 30px; }
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 11px; color: #666; }
        .info { margin-bottom: 15px; font-size: 11px; }
        .info table { width: 50%; }
        .info td { padding: 2px 0; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
        table.data th, table.data td { border: 1px solid #ddd; padding: 6px; text-align: left; vertical-align: top; word-wrap: break-word; }
        table.data th { background-color: #f4f4f4; font-weight: bold; }
        table.data td.right { text-align: right; }
        table.data th.right { text-align: right; }
        .footer { text-align: right; font-size: 12px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN KEUANGAN SEKOLAH</h1>
        <p>Rekapitulasi Penerimaan Pembayaran Tagihan</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="120"><strong>Rentang Tanggal</strong></td>
                <td>: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} s.d. {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Sekarang' }}</td>
            </tr>
            <tr>
                <td><strong>Kategori Tagihan</strong></td>
                <td>: {{ $categoryName }}</td>
            </tr>
            <tr>
                <td><strong>Waktu Cetak</strong></td>
                <td>: {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 14%;">Tanggal Bayar</th>
                <th style="width: 18%;">Ref Number</th>
                <th style="width: 24%;">Siswa & Kelas</th>
                <th style="width: 18%;">Kategori Tagihan</th>
                <th style="width: 10%;">Metode</th>
                <th style="width: 12%;" class="right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $trx)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $trx->paid_at ? \Carbon\Carbon::parse($trx->paid_at)->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $trx->reference_number }}</td>
                    <td>
                        <strong>{{ $trx->bill->student->name ?? '-' }}</strong><br>
                        <span style="font-size: 10px; color:#555;">{{ $trx->bill->student->classroom->name ?? '-' }}</span>
                    </td>
                    <td>{{ $trx->bill->paymentCategory->name ?? '-' }}</td>
                    <td>{{ strtoupper($trx->payment_type) }}</td>
                    <td class="right">{{ number_format($trx->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #888;">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="right">TOTAL PENERIMAAN</th>
                <th class="right">Rp {{ number_format($totalAmount, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

</body>
</html>
