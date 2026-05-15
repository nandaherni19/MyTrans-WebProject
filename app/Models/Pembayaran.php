<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'tr_pembayaran';
    protected $primaryKey = 'id_pembayaran';
    public $timestamps = false;

    protected $fillable = [
        'id_booking',
        'jumlah_bayar',
        'tanggal_bayar',
        'metode_pembayaran',
        'transaction_status',
        'kode_pembayaran',
        'jumlah_refund',
        'status_refund',
        'created_at',
    ];

    protected $casts = [
        'id_booking' => 'integer',
        'jumlah_bayar' => 'integer',
        'tanggal_bayar' => 'datetime',
        'created_at' => 'datetime',

    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking', 'id_booking');
    }

    public function paymentGateway()
    {
        return $this->hasOne(PaymentGateway::class, 'id_pembayaran', 'id_pembayaran');
    }
}