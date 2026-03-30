<?php
namespace App\Http\Controllers;

use App\Models\Loan;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
public function downloadAllLoansReport()
{
    // Ambil semua data peminjaman, atau bisa filter yang 'finished' saja
    $loans = Loan::with(['user', 'fine'])->get();

    $pdf = Pdf::loadView('pdf.all-loans-report', [
        'loans' => $loans,
        'title' => 'Laporan Rekapitulasi Peminjaman',
        'date' => now()->format('d M Y')
    ]);

    return $pdf->download("rekap-peminjaman-" . date('Y-m-d') . ".pdf");
}
}