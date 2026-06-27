<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        MidtransConfig::$serverKey    = config('midtrans.server_key');
        MidtransConfig::$clientKey    = config('midtrans.client_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized  = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds        = config('midtrans.is_3ds');
    }

    /**
     * Create a Snap token and transaction for the given bill.
     */
    public function pay(Request $request, Bill $bill)
    {
        $student = auth()->user()->student;

        // Ensure student owns this bill
        if ($bill->student_id !== $student->id) {
            abort(403, 'Akses ditolak.');
        }

        // Ensure bill is not already paid
        if ($bill->status === 'paid') {
            return back()->with('error', 'Tagihan ini sudah lunas.');
        }

        // Validate custom amount (partial payment support)
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $amount = (int) $request->amount;
        $remaining = (int) ($bill->amount - $bill->total_paid);

        if ($amount > $remaining) {
            return back()->with('error', 'Nominal pembayaran melebihi sisa tagihan.');
        }

        try {
            DB::beginTransaction();

            // Create a pending transaction
            $orderId = Transaction::generateOrderId();
            $transaction = Transaction::create([
                'order_id'     => $orderId,
                'bill_id'      => $bill->id,
                'amount'       => $amount,
                'payment_type' => 'midtrans',
                'status'       => 'pending',
                'expired_at'   => now()->addHours(24),
            ]);

            // Build Midtrans Snap params
            $params = [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => $amount,
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email'      => auth()->user()->email,
                    'phone'      => $student->phone ?? '',
                ],
                'item_details' => [
                    [
                        'id'       => 'BILL-' . $bill->id,
                        'price'    => $amount,
                        'quantity' => 1,
                        'name'     => substr($bill->paymentCategory->name ?? 'Pembayaran Sekolah', 0, 50),
                    ],
                ],
                'callbacks' => [
                    'finish' => route('student.payment.finish'),
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            // Store snap token in the transaction
            $transaction->update([
                'snap_token' => $snapToken,
            ]);

            DB::commit();

            return response()->json([
                'snap_token' => $snapToken,
                'order_id'   => $orderId,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gagal membuat transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Page after Midtrans payment finished (redirect from Snap).
     */
    public function finish(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Midtrans Finish Redirect Data:', $request->all());
        
        $orderId = $request->query('order_id');

        if ($orderId) {
            $transaction = Transaction::where('order_id', $orderId)->first();
            
            // Jika status masih pending, kita proaktif cek ke Midtrans (fallback untuk Webhook yang gagal masuk di localhost)
            if ($transaction && $transaction->status === 'pending') {
                try {
                    $status = \Midtrans\Transaction::status($orderId);
                    
                    if (in_array($status->transaction_status, ['capture', 'settlement'])) {
                        $transaction->markAsSuccess((array) $status);
                    } elseif (in_array($status->transaction_status, ['deny', 'cancel', 'failure'])) {
                        $transaction->markAsFailed((array) $status);
                    } elseif ($status->transaction_status === 'expire') {
                        $transaction->markAsExpired((array) $status);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Midtrans fallback status check error: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('student.transactions.index')
            ->with('success', 'Terima kasih! Status pembayaran Anda telah diperbarui.');
    }
}
