<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        if (!$student) {
            return redirect('/')->with('error', 'Profil siswa tidak ditemukan.');
        }

        $transactions = $student->transactions()->with(['bill.paymentCategory', 'bill.academicYear'])
                              ->orderBy('created_at', 'desc')
                              ->paginate(15);
                              
        return view('student.transactions.index', compact('transactions'));
    }

    public function receipt($id)
    {
        $student = auth()->user()->student;
        if (!$student) {
            return redirect('/')->with('error', 'Profil siswa tidak ditemukan.');
        }

        $transaction = \App\Models\Transaction::with(['bill.student.classroom', 'bill.paymentCategory'])
            ->whereHas('bill', function($q) use ($student) {
                $q->where('student_id', $student->id);
            })->findOrFail($id);
            
        if ($transaction->status !== 'success') {
            return back()->with('error', 'Kwitansi hanya tersedia untuk transaksi yang sudah lunas/berhasil.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.transactions.receipt', compact('transaction'));
        
        $filename = 'Kwitansi_' . $transaction->reference_number . '.pdf';
        
        return $pdf->download($filename);
    }
}
