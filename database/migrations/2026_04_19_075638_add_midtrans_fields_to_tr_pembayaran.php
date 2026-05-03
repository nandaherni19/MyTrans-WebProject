<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::table('tr_pembayaran', function (Blueprint $table) {
            $table->string('midtrans_order_id')->nullable()->after('id_booking');
            $table->string('midtrans_transaction_id')->nullable()->after('midtrans_order_id');
            $table->string('payment_type')->nullable()->after('midtrans_transaction_id');
            $table->text('qr_url')->nullable()->after('payment_type');
            $table->dateTime('expired_at')->nullable()->after('qr_url');
            $table->longText('midtrans_response')->nullable()->after('expired_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_pembayaran', function (Blueprint $table) {
             $table->dropColumn([
                'midtrans_order_id',
                'midtrans_transaction_id',
                'payment_type',
                'qr_url',
                'expired_at',
                'midtrans_response'
            ]);
        });
    }
};
