<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Cek User & Token
        $user  = Session::get('user');
        $token = Session::get('auth_token');

        if (empty($user) || empty($token)) {
            return redirect()->route('login')
                ->withErrors(['_global' => 'Silakan login terlebih dahulu.']);
        }

        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $userArr = is_object($user) ? (array) $user : (array) $user;

        // 2. Siapkan Variabel Default
        $rekapStatus  = [];
        $totalLaporan = 0;
        $dataTerbaru  = [];
        $apiError     = null;

        // ---------------------------------------------------------
        // 🔥 LOGIC GRAFIK: Pre-fill 7 Hari Terakhir
        // Agar grafik SELALU MUNCUL walaupun data API kosong
        // ---------------------------------------------------------
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateKey = Carbon::now()->subDays($i)->format('Y-m-d'); // Key untuk pencocokan: 2025-01-31
            $dateLabel = Carbon::now()->subDays($i)->format('d M'); // Label Chart: 31 Jan

            $chartData[$dateKey] = [
                'label' => $dateLabel,
                'value' => 0 // Default 0
            ];
        }

        // 3. Panggil API Dashboard
        try {
            $resp = Http::withToken($token)
                ->acceptJson()
                ->get($baseUrl . '/api/dashboard');

            if ($resp->ok()) {
                $json = $resp->json();

                $rekapStatus  = $json['rekap_status'] ?? [];
                $totalLaporan = (int) ($json['total'] ?? 0);
                $dataTerbaru  = $json['data_terbaru'] ?? [];
                $rekapTanggal = $json['rekap_tanggal'] ?? [];

                // Mapping Data API ke Struktur Chart yang sudah kita buat
                if (is_array($rekapTanggal)) {
                    foreach ($rekapTanggal as $row) {
                        $tglApi = $row['tanggal'] ?? ''; // Format: 2025-01-31

                        // Jika tanggal dari API ada dalam range 7 hari kita, update nilainya
                        if (isset($chartData[$tglApi])) {
                            $chartData[$tglApi]['value'] = (int) ($row['total'] ?? 0);
                        }
                    }
                }
            } else {
                $apiError = 'Gagal memuat data. Status: ' . $resp->status();
            }
        } catch (\Throwable $e) {
            $apiError = 'Terjadi kesalahan koneksi ke server API.';
        }

        // Pisahkan kembali ke Array untuk dikirim ke View
        // array_column akan mengambil value urut sesuai tanggal
        $chartLabels = array_column($chartData, 'label');
        $chartValues = array_column($chartData, 'value');

        // 4. Return ke View
        return view('backend.dashboard.index', [
            'user'         => $userArr,
            'rekapStatus'  => $rekapStatus,
            'totalLaporan' => $totalLaporan,
            'dataTerbaru'  => $dataTerbaru,
            'chartLabels'  => $chartLabels,
            'chartValues'  => $chartValues,
            'apiError'     => $apiError
        ]);
    }
}
