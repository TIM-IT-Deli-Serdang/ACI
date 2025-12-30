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
            return redirect()->route('login')->withErrors(['_global' => 'Silakan login terlebih dahulu.']);
        }

        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        if (empty($baseUrl)) {
            return redirect()->route('login')->withErrors(['_global' => 'API_BASE_URL belum diset di .env']);
        }

        // Normalisasi user ke array
        $userArr = is_object($user) ? (array) $user : (array) $user;

        // Inisialisasi variabel default (PENTING agar tidak error di view)
        $rekapStatus = [];
        $totalLaporan = 0;
        $kecamatanCounts = [];
        $chartLabels = [];
        $chartValues = [];

        // =========================
        // 2) REKAP PER STATUS
        // =========================
        try {
            $respStatus = Http::withToken($token)->timeout(5)->get($baseUrl . '/api/rekap');
            
            if ($respStatus->status() === 401) {
                return redirect()->route('login')->withErrors(['_global' => 'Token expired. Silakan login ulang.']);
            }

            if ($respStatus->ok()) {
                $json = $respStatus->json() ?? [];
                $rekapStatus = $json['rekap_status'] ?? [];
                $totalLaporan = (int)($json['total'] ?? array_sum($rekapStatus));
            }
        } catch (\Throwable $e) {
            Log::error('Dashboard Error (Rekap Status): ' . $e->getMessage());
            // Lanjut saja, variabel tetap array kosong []
        }

        // =========================
        // 3) COUNT PER KECAMATAN
        // =========================
        try {
            // Ambil list kecamatan
            $respKec = Http::withToken($token)->timeout(10)->get($baseUrl . '/api/wilayah-kecamatan/datatables', [
                'draw' => 1,
                'start' => 0,
                'length' => 1000, // Ambil cukup banyak
                'search' => ['value' => ''],
            ]);

            $rows = [];
            if ($respKec->ok()) {
                $kjson = $respKec->json() ?? [];
                $rows = $kjson['data'] ?? [];
            }

            // Helper kecil untuk ambil field ID dan Nama secara aman
            $pick = function(array $row, array $keys) {
                foreach ($keys as $k) {
                    if (array_key_exists($k, $row) && $row[$k] !== null) {
                        return $row[$k];
                    }
                }
                return null;
            };

            // Susun daftar ID & Nama Kecamatan
            $kecamatanList = [];
            foreach ($rows as $r) {
                if (!is_array($r)) continue;
                $id = $pick($r, ['id', 'kecamatan_id', 'kode']);
                $nm = $pick($r, ['nama_kecamatan', 'nama', 'name', 'kecamatan']);
                
                if ($id && $nm) {
                    $kecamatanList[] = ['id' => $id, 'nama' => $nm];
                }
            }

            // Jika ada data kecamatan, ambil rekap per kecamatan
            if (!empty($kecamatanList)) {
                // Gunakan Http::pool untuk request paralel (lebih cepat)
                $responses = Http::pool(function ($pool) use ($kecamatanList, $baseUrl, $token) {
                    $reqs = [];
                    foreach ($kecamatanList as $kec) {
                        // Pastikan URL endpoint benar sesuai API backend Anda
                        $reqs[$kec['id']] = $pool->withToken($token)->get($baseUrl . '/api/rekap/kecamatan', [
                            'kecamatan_id' => $kec['id'],
                        ]);
                    }
                    return $reqs;
                });

                // Map hasil response ke array
                foreach ($kecamatanList as $kec) {
                    $id = $kec['id'];
                    $nama = $kec['nama'];
                    $total = 0;

                    if (isset($responses[$id]) && $responses[$id]->ok()) {
                        $j = $responses[$id]->json();
                        $total = (int)($j['total'] ?? 0);
                    }

                    $kecamatanCounts[] = [
                        'id' => $id,
                        'nama' => $nama,
                        'total' => $total
                    ];
                }

                // Urutkan descending berdasarkan total
                usort($kecamatanCounts, fn($a, $b) => $b['total'] <=> $a['total']);

                // Siapkan data Chart (Top 10)
                $top = array_slice($kecamatanCounts, 0, 10);
                foreach ($top as $t) {
                    $chartLabels[] = $t['nama'];
                    $chartValues[] = $t['total'];
                }
            }

        } catch (\Throwable $e) {
            Log::error('Dashboard Error (Kecamatan): ' . $e->getMessage());
            // Fallback: variabel tetap array kosong []
        }

        // Kirim ke View dengan data yang PASTI aman (bukan null)
        return view('backend.dashboard.index', [
            'user' => $userArr,
            'rekapStatus' => $rekapStatus ?? [],
            'totalLaporan' => $totalLaporan ?? 0,
            'kecamatanCounts' => $kecamatanCounts ?? [],
            'chartLabels' => $chartLabels ?? [],
            'chartValues' => $chartValues ?? [],
        ]);
    }
}