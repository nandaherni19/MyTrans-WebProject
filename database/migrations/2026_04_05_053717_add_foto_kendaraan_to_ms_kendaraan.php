<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ms_kendaraan', function (Blueprint $table) {
            $table->string('foto_kendaraan')->nullable();
        });
    }

    public function down()
    {
        Schema::table('ms_kendaraan', function (Blueprint $table) {
            $table->dropColumn('foto_kendaraan');
        });
    }
};