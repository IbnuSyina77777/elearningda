<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['bill.student.classroom', 'bill.paymentCategory']);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhereHas('bill.student.user', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();

        return view('admin.transactions.index', compact('transactions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bill_id'        => 'required|exists:bills,id',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'notes'          => 'nullable|string',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $bill = \App\Models\Bill::lockForUpdate()->findOrFail($validated['bill_id']);
            
            // Validate amount doesn't exceed remaining
            $remaining = $bill->amount - $bill->total_paid;
            if ($validated['amount'] > $remaining) {
                return back()->with('error', 'Nominal pembayaran melebihi sisa tagihan.');
            }

            // Create Transaction with correct database columns
            $transaction = Transaction::create([
                'order_id'         => 'MANUAL-' . strtoupper(uniqid()),
                'bill_id'          => $bill->id,
                'payment_type'     => $validated['payment_method'],
                'amount'           => $validated['amount'],
                'status'           => 'success',
                'paid_at'          => now(),
                'midtrans_response'=> $validated['notes'] ? ['admin_notes' => $validated['notes']] : null,
            ]);

            // Update Bill
            $bill->total_paid += $validated['amount'];
            if ($bill->total_paid >= $bill->amount) {
                $bill->status = 'paid';
            } else {
                $bill->status = 'partial';
            }
            $bill->save();

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Pembayaran berhasil dicatat.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function receipt($id)
    {
        $transaction = Transaction::with(['bill.student.classroom', 'bill.paymentCategory'])->findOrFail($id);
        
        if ($transaction->status !== 'success') {
            return back()->with('error', 'Kwitansi hanya tersedia untuk transaksi yang sudah lunas/berhasil.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.transactions.receipt', compact('transaction'));
        
        $filename = 'Kwitansi_' . $transaction->reference_number . '.pdf';
        
        return $pdf->download($filename);
    }
}
