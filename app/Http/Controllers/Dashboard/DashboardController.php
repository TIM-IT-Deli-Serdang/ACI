<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil user + token dari session
        $user  = Session::get('user');
        $token = Session::get('auth_token');

        if (empty($user) || empty($token)) {
            return redirect()->route('login')
                ->withErrors(['_global' => 'Silakan login terlebih dahulu.']);
        }

        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        
        // Normalisasi user ke array
        $userArr = is_object($user) ? (array) $user : (array) $user;

        // ---------------------------------------------------------
        // ✅ 2. DETEKSI ROLE
        // ---------------------------------------------------------
        $role = '';
        if (isset($userArr['roles']) && is_array($userArr['roles']) && count($userArr['roles']) > 0) {
            $firstRole = $userArr['roles'][0];
            if (is_string($firstRole)) {
                $role = $firstRole;
            } elseif (is_array($firstRole)) {
                $role = $firstRole['name'] ?? '';
            }
        }
        // Fallback
        if (empty($role)) {
            $role = $userArr['role'] ?? Session::get('role') ?? '';
        }
        $role = strtolower(trim((string) $role));


        // ---------------------------------------------------------
        // KONDISI 1: MASYARAKAT (Data Pribadi)
        // ---------------------------------------------------------
        if ($role === 'masyarakat') {
            $rekapStatus  = [];
            $totalLaporan = 0;
            $dataTerbaru  = [];
            $chartLabels  = [];
            $chartValues  = [];

            try {
                // Endpoint khusus masyarakat
                $resp = Http::withToken($token)->acceptJson()->get($baseUrl . '/api/dashboard/masyarakat');
                
                if ($resp->ok()) {
                    $json = $resp->json();
                    $rekapStatus  = $json['rekap_status'] ?? [];
                    $totalLaporan = (int) ($json['total'] ?? 0);
                    $rekapTanggal = $json['rekap_tanggal'] ?? [];
                    $dataTerbaru  = $json['data_terbaru'] ?? [];

                    // Proses Chart
                    if (is_array($rekapTanggal)) {
                        usort($rekapTanggal, function ($a, $b) {
                            return strcmp((string)($a['tanggal'] ?? ''), (string)($b['tanggal'] ?? ''));
                        });
                        foreach ($rekapTanggal as $row) {
                            $chartLabels[] = (string) ($row['tanggal'] ?? '');
                            $chartValues[] = (int) ($row['total'] ?? 0);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Silent fail / Kosongkan data
            }

            return view('backend.dashboard.masyarakat', [
                'user'         => $userArr,
                'rekapStatus'  => $rekapStatus,
                'totalLaporan' => $totalLaporan,
                'dataTerbaru'  => $dataTerbaru,
                'chartLabels'  => $chartLabels,
                'chartValues'  => $chartValues,
            ]);
        }

        // ---------------------------------------------------------
        // KONDISI 2: INTERNAL (Superadmin, SDA, UPT)
        // Semuanya pakai API yang sama & View yang sama
        // ---------------------------------------------------------
        
        // Daftar role yang boleh lihat dashboard admin
        $internalRoles = ['superadmin', 'super admin', 'sda', 'upt'];

        if (in_array($role, $internalRoles)) {
            
            $rekapStatus  = [];
            $totalLaporan = 0;
            $dataTerbaru  = [];
            $chartLabels  = [];
            $chartValues  = [];
            
            try {
                // Panggil endpoint global (Backend otomatis filter berdasarkan token UPT/SDA/Admin)
                $resp = Http::withToken($token)->acceptJson()->get($baseUrl . '/api/dashboard');
                
                if ($resp->ok()) {
                    $json = $resp->json();
                    $rekapStatus  = $json['rekap_status'] ?? [];
                    $totalLaporan = (int) ($json['total'] ?? 0);
                    $rekapTanggal = $json['rekap_tanggal'] ?? [];
                    $dataTerbaru  = $json['data_terbaru'] ?? [];

                    // Proses Chart
                    if (is_array($rekapTanggal)) {
                        usort($rekapTanggal, function ($a, $b) {
                            return strcmp((string)($a['tanggal'] ?? ''), (string)($b['tanggal'] ?? ''));
                        });
                        foreach ($rekapTanggal as $row) {
                            $chartLabels[] = (string) ($row['tanggal'] ?? '');
                            $chartValues[] = (int) ($row['total'] ?? 0);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Error handling sederhana agar halaman tidak crash
            }

            // Arahkan UPT & SDA ke tampilan Superadmin (karena layoutnya 100% sama)
            return view('backend.dashboard.superadmin', [
                'user'         => $userArr,
                'rekapStatus'  => $rekapStatus,
                'totalLaporan' => $totalLaporan,
                'dataTerbaru'  => $dataTerbaru,
                'chartLabels'  => $chartLabels,
                'chartValues'  => $chartValues,
            ]);
        }

        // ---------------------------------------------------------
        // DEFAULT (Jika role tidak dikenal)
        // ---------------------------------------------------------
        return view('backend.dashboard.index', [
            'user' => $userArr
        ]);
    }
}