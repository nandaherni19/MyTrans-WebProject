<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ms_kendaraan', function (Blueprint $table) {
            $table->enum('status_kendaraan', [
                'tersedia',
                'tidak_tersedia',
                'maintenance'
            ])->change();
        });
    }

    public function down()
    {
        Schema::table('ms_kendaraan', function (Blueprint $table) {
            $table->string('status_kendaraan')->change();
        });
    }
};
