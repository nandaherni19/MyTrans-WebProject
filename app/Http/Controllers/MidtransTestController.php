<?php

namespace App\Http\Controllers;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransTestController extends Controller
{
    public function config()
    {
        return response()->json(config('midtrans'));
    }

    public function env()
    {
        return response()->json([
            'env_server_key' => env('MIDTRANS_SERVER_KEY'),
            'env_client_key' => env('MIDTRANS_CLIENT_KEY'),
            'env_is_production' => env('MIDTRANS_IS_PRODUCTION'),
        ]);
    }

    public function snapToken()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => 'TEST-' . time(),
                'gross_amount' => 10000,
            ],
            'customer_details' => [
                'first_name' => 'Amalia',
                'email' => 'amaliakhoirun08@gmail.com',
                'phone' => '081234567890',
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'snap_token' => $snapToken,
        ]);
    }
}