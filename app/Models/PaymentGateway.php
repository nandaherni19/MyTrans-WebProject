<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $table = 'tr_payment_gateway';
    protected $primaryKey = 'id_payment_gateway';

    public $timestamps = true;

    protected $fillable = [
        'id_pembayaran',
        'gateway_name',
        'gateway_order_id',
        'gateway_transaction_id',
        'payment_type',
        'qr_url',
        'expired_at',
        'transaction_status',
        'raw_response',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'id_pembayaran', 'id_pembayaran');
    }
}