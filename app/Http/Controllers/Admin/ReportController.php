<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\PaymentCategory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $categories = PaymentCategory::all();
        $query = Transaction::with(['bill.student.classroom', 'bill.paymentCategory'])
            ->where('status', 'success');

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $categoryId = $request->query('category_id');

        if ($startDate) {
            $query->whereDate('paid_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('paid_at', '<=', $endDate);
        }
        if ($categoryId) {
            $query->whereHas('bill', function($q) use ($categoryId) {
                $q->where('payment_category_id', $categoryId);
            });
        }
        
        $search = $request->query('search');
        if ($search) {
            $searchLow = strtolower($search);
            $query->whereHas('bill.student', function($q) use ($searchLow) {
                $q->where(function($sq) use ($searchLow) {
                    $sq->whereHas('user', function($uq) use ($searchLow) {
                        $uq->whereRaw('LOWER(name) like ?', ["%{$searchLow}%"]);
                    })
                    ->orWhere('nis', 'like', "%{$searchLow}%")
                    ->orWhere('nisn', 'like', "%{$searchLow}%");
                });
            });
        }

        $transactions = $query->orderBy('paid_at', 'desc')->get();
        $totalAmount = $transactions->sum('amount');

        return view('admin.reports.index', compact('transactions', 'categories', 'startDate', 'endDate', 'categoryId', 'totalAmount'));
    }

    public function exportPdf(Request $request)
    {
        $query = Transaction::with(['bill.student.classroom', 'bill.paymentCategory'])
            ->where('status', 'success');

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $categoryId = $request->query('category_id');
        $categoryName = 'Semua Kategori';

        if ($startDate) {
            $query->whereDate('paid_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('paid_at', '<=', $endDate);
        }
        if ($categoryId) {
            $query->whereHas('bill', function($q) use ($categoryId) {
                $q->where('payment_category_id', $categoryId);
            });
            $categoryName = PaymentCategory::find($categoryId)->name ?? 'Unknown';
        }
        
        $search = $request->query('search');
        if ($search) {
            $searchLow = strtolower($search);
            $query->whereHas('bill.student', function($q) use ($searchLow) {
                $q->where(function($sq) use ($searchLow) {
                    $sq->whereHas('user', function($uq) use ($searchLow) {
                        $uq->whereRaw('LOWER(name) like ?', ["%{$searchLow}%"]);
                    })
                    ->orWhere('nis', 'like', "%{$searchLow}%")
                    ->orWhere('nisn', 'like', "%{$searchLow}%");
                });
            });
        }

        $transactions = $query->orderBy('paid_at', 'asc')->get();
        $totalAmount = $transactions->sum('amount');

        $pdf = Pdf::loadView('admin.reports.pdf', compact('transactions', 'startDate', 'endDate', 'categoryName', 'totalAmount'))
                  ->setPaper('a4', 'landscape');
        
        $filename = 'Laporan_Keuangan';
        if ($startDate) $filename .= '_' . $startDate;
        if ($endDate) $filename .= '_to_' . $endDate;
        
        return $pdf->download($filename . '.pdf');
    }
}
