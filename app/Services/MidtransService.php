<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\CoreApi;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createQris(array $data)
    {
        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $data['order_id'],
                'gross_amount' => (int) $data['gross_amount'],
            ],
            'customer_details' => [
                'first_name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ],
            'custom_expiry' => [
                'order_time' => now()->format('Y-m-d H:i:s O'),
                'expiry_duration' => 24,
                'unit' => 'hour',
            ],
            'qris' => [
                'acquirer' => 'gopay'
            ]
        ];

        return CoreApi::charge($params);
    }
}