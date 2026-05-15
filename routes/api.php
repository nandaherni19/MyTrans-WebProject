<?php

use Illuminate\Support\Facades\Route;
use App\Models\Kota;
use App\Http\Controllers\User\MidtransCallbackController;


Route::get('/kota-by-provinsi/{id}', function ($id) {
    return Kota::where('id_provinsi', $id)
        ->select('id_kota', 'nama_kota')
        ->get();
});

// routes/api.php
Route::post('/midtrans/notification', [MidtransCallbackController::class, 'handle']);
Route::post('/midtrans/webhook', [MidtransCallbackController::class, 'handle']);