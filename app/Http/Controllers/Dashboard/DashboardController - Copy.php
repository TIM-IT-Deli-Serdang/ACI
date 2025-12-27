<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard menggunakan data user yang tersimpan di session.
     * Tidak memanggil API /api/me.
     */
    public function index(Request $request)
    {
        // Ambil user dari session (disimpan saat login)
        $user = Session::get('user');

        // Jika tidak ada data user di session -> redirect ke login
        if (empty($user)) {
            return redirect()->route('login')->withErrors(['_global' => 'Silakan login terlebih dahulu.']);
        }

        // Pastikan user punya atribut yang kita butuhkan (bisa array atau model)
        // Normalisasi: jika object convert ke array
        if (is_object($user)) {
            $userArr = (array) $user;
        } else {
            $userArr = (array) $user;
        }

        // Data tambahan yang ingin ditampilkan di dashboard
        $data = [
            'user' => $userArr,
            // tambahkan elemen lain bila perlu, mis. stats lokal
        ];

        return view('backend.dashboard.index', $data);
    }

    
}
