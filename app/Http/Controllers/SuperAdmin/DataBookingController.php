<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataBookingController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 'index');
        $id = $request->query('id', 'BK001');

        return view('dashboard.superadmin.kelola-data-booking', compact('page', 'id'));
    }

    public function update(Request $request, $id)
    {
        return redirect()
            ->route('dashboard.superadmin.kelola-data-booking', [
                'page' => 'edit',
                'id' => $id,
            ])
            ->with('success', 'Perubahan berhasil diproses.');
    }
}