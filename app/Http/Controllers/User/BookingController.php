<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PaketWisata;

class BookingController extends Controller
{
    public function qris(Request $request)
    {
        $user = Auth::user();
        $selectedPaketId = $request->session()->get('selected_paket');
        $paket = null;
        $showWarning = false;

        if ($selectedPaketId) {
            $paket = PaketWisata::find($selectedPaketId);
        }

        if (!$paket) {
            $showWarning = true;
        }

        return view('dashboard.user.booking', [
            'page' => 'qris',
            'user' => $user,
            'paket' => $paket,
            'showWarning' => $showWarning,
        ]);
    }
}