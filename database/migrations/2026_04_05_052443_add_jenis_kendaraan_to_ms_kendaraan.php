<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ms_kendaraan', function (Blueprint $table) {
            $table->string('jenis_kendaraan')->after('nama_kendaraan');
        });
    }

    public function down()
    {
        Schema::table('ms_kendaraan', function (Blueprint $table) {
            $table->dropColumn('jenis_kendaraan');
        });
    }
};
