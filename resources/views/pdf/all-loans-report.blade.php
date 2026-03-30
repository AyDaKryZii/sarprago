<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            text-transform: uppercase;
            font-size: 22px;
            color: #1a202c;
        }
        .header p {
            margin: 5px 0;
            font-size: 13px;
            color: #718096;
        }
        .info-meta {
            margin-bottom: 20px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }
        th {
            background-color: #4a5568;
            color: white;
            text-align: left;
            padding: 10px;
            font-size: 12px;
            text-transform: uppercase;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-finished { background-color: #c6f6d5; color: #22543d; }
        .status-on_going { background-color: #bee3f8; color: #2a4365; }
        .status-overdue { background-color: #fed7d7; color: #822727; }
        .text-right { text-align: right; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            color: #718096;
        }
        .total-row {
            background-color: #edf2f7 !important;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Rekapitulasi Peminjaman</h1>
        <p>Sistem Manajemen Inventaris & Sarpras</p>
    </div>

    <div class="info-meta">
        Tanggal Cetak: <strong>{{ now()->format('d F Y H:i') }}</strong><br>
        Dicetak Oleh: <strong>{{ auth()->user()->name }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode</th>
                <th width="20%">Peminjam</th>
                <th width="15%">Tgl Pinjam</th>
                <th width="15%">Status</th>
                <th class="text-right" width="15%">Denda</th>
            </tr>
        </thead>
        <tbody>
            @php $totalFine = 0; @endphp
            @foreach($loans as $index => $loan)
                @php 
                    $fineAmount = $loan->fine?->amount ?? 0;
                    $totalFine += $fineAmount;
                    $isOverdue = $loan->status === 'on_going' && $loan->due_at?->isPast();
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $loan->loan_code }}</strong></td>
                    <td>{{ $loan->user->name }}</td>
                    <td>{{ $loan->borrowed_at?->format('d/m/Y') ?? '-' }}</td>
                    <td>
                        @if($isOverdue)
                            <span class="badge status-overdue">OVERDUE</span>
                        @else
                            <span class="badge status-{{ $loan->status }}">
                                {{ str_replace('_', ' ', $loan->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="text-right">
                        {{ $fineAmount > 0 ? 'Rp ' . number_format($fineAmount, 0, ',', '.') : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL PENDAPATAN DENDA:</td>
                <td class="text-right">Rp {{ number_format($totalFine, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Laporan ini dihasilkan otomatis oleh sistem pada {{ date('d/m/Y') }}</p>
    </div>
</body>
</html>