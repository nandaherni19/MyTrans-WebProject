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
        Schema::table('ms_request_wisata', function (Blueprint $table) {
            $table->date('tanggal_kembali')->after('tanggal_keberangkatan');
            $table->string('titik_jemput')->after('id_kota_asal');
            $table->string('alamat')->after('titik_jemput');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_wisata', function (Blueprint $table) {
            //
        });
    }
};
