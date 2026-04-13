<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ms_paket_wisata', function (Blueprint $table) {
            $table->string('lokasi')->nullable();
            $table->text('fasilitas_didapat')->nullable();
            $table->text('fasilitas_tidak')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ms_paket_wisata', function (Blueprint $table) {
            $table->dropColumn(['lokasi', 'fasilitas_didapat', 'fasilitas_tidak']);
        });
    }
};