<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ms_paket_wisata', function (Blueprint $table) {

            // hapus kolom
            $table->dropColumn('fasilitas_tidak');

            // tambah kolom kapasitas
            $table->integer('kapasitas')->after('nama_paket');
        });
    }

    public function down()
    {
        Schema::table('ms_paket_wisata', function (Blueprint $table) {

            // balikin kalau rollback
            $table->text('fasilitas_tidak')->nullable();

            $table->dropColumn('kapasitas');
        });
    }
};