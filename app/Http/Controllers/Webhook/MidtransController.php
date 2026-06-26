<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config as MidtransConfig;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function __construct()
    {
        MidtransConfig::$serverKey    = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
    }

    /**
     * Handle Midtrans webhook notification.
     * Called by Midtrans server when payment status changes.
     */
    public function handle(Request $request)
    {
        try {
            $notification = new Notification();

            $orderId           = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus       = $notification->fraud_status ?? null;
            $paymentType       = $notification->payment_type;

            Log::info('Midtrans Webhook:', [
                'order_id'           => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status'       => $fraudStatus,
                'payment_type'       => $paymentType,
            ]);

            $transaction = Transaction::where('order_id', $orderId)->first();

            if (!$transaction) {
                Log::warning("Midtrans Webhook: Transaction not found for order_id: {$orderId}");
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            // Prevent updating already-finalized transactions
            if (in_array($transaction->status, ['success', 'failed'])) {
                return response()->json(['message' => 'Transaction already finalized'], 200);
            }

            // Update payment type from Midtrans
            $transaction->update(['payment_type' => $paymentType]);

            // Process based on transaction status
            if ($transactionStatus === 'capture') {
                // For credit card: check fraud status
                if ($fraudStatus === 'accept') {
                    $transaction->markAsSuccess($request->all());
                } elseif ($fraudStatus === 'challenge') {
                    $transaction->update([
                        'status'            => 'challenge',
                        'midtrans_response' => $request->all(),
                    ]);
                }
            } elseif ($transactionStatus === 'settlement') {
                // Most payment types end up here
                $transaction->markAsSuccess($request->all());
            } elseif ($transactionStatus === 'pending') {
                $transaction->update([
                    'status'            => 'pending',
                    'midtrans_response' => $request->all(),
                ]);
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'failure'])) {
                $transaction->markAsFailed($request->all());
            } elseif ($transactionStatus === 'expire') {
                $transaction->markAsExpired($request->all());
            }

            return response()->json(['message' => 'OK'], 200);
        } catch (\Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }
}
