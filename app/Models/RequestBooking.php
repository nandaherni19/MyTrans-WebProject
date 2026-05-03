<?php
Schema::create('request_bookings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id');
    $table->string('tujuan');
    $table->date('tanggal_keberangkatan');
    $table->date('tanggal_kembali');
    $table->integer('jumlah_peserta');
    $table->integer('budget_min')->nullable();
    $table->integer('budget_max')->nullable();
    $table->text('keterangan')->nullable();
    $table->string('status_request')->default('pending');
    $table->timestamps();
});                                                         