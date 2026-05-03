<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PaymentGateway;

use Carbon\Carbon;

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Midtrans callback masuk', $request->all());

        $serverKey = config('midtrans.server_key');

        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $signatureKey = $request->input('signature_key');

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $expectedSignature) {
            Log::warning('Signature Midtrans tidak valid', [
                'order_id' => $orderId,
            ]);

            return response()->json([
                'message' => 'Invalid signature'
            ], 403);
        }

        $pembayaran = Pembayaran::where('kode_pembayaran', $orderId)->first();

        if (!$pembayaran) {
            Log::warning('Data pembayaran tidak ditemukan', [
                'order_id' => $orderId,
            ]);

            return response()->json([
                'message' => 'Pembayaran tidak ditemukan'
            ], 404);
        }

        $booking = Booking::find($pembayaran->id_booking);

        $transactionStatus = $request->input('transaction_status');
        $fraudStatus = $request->input('fraud_status');

        if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus !== 'challenge')) {
    $statusPembayaran = 'berhasil';
} elseif ($transactionStatus === 'pending') {
    $statusPembayaran = 'pending';
} elseif ($transactionStatus === 'expire') {
    $statusPembayaran = 'expired';
} else {
    $statusPembayaran = 'gagal';
}

// $pembayaran->update([
//     'transaction_status' => $statusPembayaran,
//     'tanggal_bayar' => $statusPembayaran === 'berhasil' ? now() : null,
// ]);

// PaymentGateway::where('gateway_order_id', $orderId)->update([
//     'gateway_transaction_id' => $request->input('transaction_id'),
//     'transaction_status' => $transactionStatus,
//     'raw_response' => json_encode($request->all()),
// ]);

//     if ($booking) {
//     if ($statusPembayaran === 'berhasil') {
//         $booking->status_booking = 'aktif';
//     } elseif (in_array($statusPembayaran, ['gagal', 'expired'])) {
//         $booking->status_booking = 'batal';
//     } else {
//         $booking->status_booking = 'pending';
//     }

//     $booking->save();
// }

if ($pembayaran->transaction_status !== 'berhasil') {
    $pembayaran->update([
        'transaction_status' => $statusPembayaran,
        'tanggal_bayar' => $statusPembayaran === 'berhasil' ? now() : null,
    ]);
}

PaymentGateway::where('gateway_order_id', $orderId)->update([
    'gateway_transaction_id' => $request->input('transaction_id'),
    'transaction_status' => $transactionStatus,
    'raw_response' => json_encode($request->all()),
]);

if ($booking && $booking->status_booking !== 'aktif') {
    if ($statusPembayaran === 'berhasil') {
        $booking->status_booking = 'aktif';
    } elseif (in_array($statusPembayaran, ['gagal', 'expired'])) {
        $booking->status_booking = 'batal';
    } else {
        $booking->status_booking = 'pending';
    }
    $booking->save();
}

        return response()->json([
            'message' => 'Notification handled successfully'
        ]);
    }
    // MidtransController.php — tambahkan logika ini di webhook handler
    public function webhook(Request $request)
    {
        $payload           = $request->all();
        $orderId           = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus       = $payload['fraud_status'] ?? null;

        // order_id format: PAY-{id_booking}-{timestamp}
        $parts     = explode('-', $orderId);
        $bookingId = $parts[1] ?? null;

        if (!$bookingId) return response()->json(['message' => 'Invalid order id'], 400);

        $pembayaran = Pembayaran::where('id_booking', $bookingId)
            ->orderByDesc('id_pembayaran')
            ->first();

        if (!$pembayaran) return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);

        $booking = Booking::find($bookingId);
        if (!$booking) return response()->json(['message' => 'Booking tidak ditemukan'], 404);

        // Tentukan status pembayaran
        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            $statusPembayaran = 'berhasil';
        } elseif ($transactionStatus === 'settlement') {
            $statusPembayaran = 'berhasil';
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'failure'])) {
            $statusPembayaran = 'gagal';
        } elseif ($transactionStatus === 'expire') {
            $statusPembayaran = 'expired';
        } else {
            $statusPembayaran = 'pending';
        }

        // Update pembayaran
        $pembayaran->update([
            'transaction_status' => $statusPembayaran,
            'tanggal_bayar'      => in_array($statusPembayaran, ['berhasil']) ? now() : null,
        ]);

        // Update status booking otomatis
        if ($statusPembayaran === 'berhasil') {
            $statusBooking = $booking->tanggal_kembali && Carbon::parse($booking->tanggal_kembali)->isPast()
                ? 'selesai'
                : 'aktif';
        } elseif (in_array($statusPembayaran, ['expired', 'gagal'])) {
            $statusBooking = 'batal';
        } else {
            $statusBooking = $booking->status_booking;
        }

        $booking->update([
            'status_booking' => $statusBooking,
            'updated_at'     => now(),
        ]);

        return response()->json(['message' => 'Webhook handled']);
    }
}