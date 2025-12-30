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
        if (empty($baseUrl)) {
            return redirect()->route('login')
                ->withErrors(['_global' => 'API_BASE_URL belum diset di .env']);
        }

        // Normalisasi user ke array
        $userArr = is_object($user) ? (array) $user : (array) $user;

        // =========================================================
        // ✅ DETEKSI ROLE (prioritas: userArr['roles'][0])
        // =========================================================
        $role = '';

        // Case 1: roles (array) -> ambil role pertama
        if (isset($userArr['roles']) && is_array($userArr['roles']) && count($userArr['roles']) > 0) {
            $firstRole = $userArr['roles'][0];

            if (is_string($firstRole)) {
                $role = $firstRole;
            } elseif (is_array($firstRole)) {
                $role = $firstRole['name']
                    ?? $firstRole['role_name']
                    ?? $firstRole['roleName']
                    ?? '';
            } elseif (is_object($firstRole)) {
                $role = $firstRole->name
                    ?? $firstRole->role_name
                    ?? $firstRole->roleName
                    ?? '';
            }
        }

        // Case 2: fallback kalau roles tidak ada
        if (empty($role)) {
            $role = $userArr['role']
                ?? $userArr['role_name']
                ?? $userArr['roleName']
                ?? Session::get('role')
                ?? '';
        }

        $role = strtolower(trim((string) $role));

        // =========================================================
        // ✅ DASHBOARD MASYARAKAT (Swagger)
        // GET /api/dashboard/masyarakat
        // View: backend.dashboard.masyarakat
        // =========================================================
        if ($role === 'masyarakat') {

            $rekapStatus  = [];
            $totalLaporan = 0;
            $rekapTanggal = [];
            $dataTerbaru  = [];

            try {
                $url  = $baseUrl . '/api/dashboard/masyarakat';
                $resp = Http::withToken($token)->acceptJson()->get($url);

                if ($resp->status() === 401) {
                    return redirect()->route('login')
                        ->withErrors(['_global' => 'Token expired. Silakan login ulang.']);
                }

                if (!$resp->ok()) {
                    return view('backend.dashboard.masyarakat', [
                        'user'         => $userArr,
                        'rekapStatus'  => [],
                        'totalLaporan' => 0,
                        'dataTerbaru'  => [],
                        'chartLabels'  => [],
                        'chartValues'  => [],
                        'apiError'     => $resp->json('message')
                            ?? ('Gagal ambil dashboard masyarakat (HTTP ' . $resp->status() . ')'),
                    ]);
                }

                $json = $resp->json() ?? [];

                $rekapStatus  = $json['rekap_status'] ?? [];
                $totalLaporan = (int) ($json['total'] ?? 0);
                $rekapTanggal = $json['rekap_tanggal'] ?? [];

                // opsional kalau nanti swagger tambah
                $dataTerbaru  = $json['data_terbaru'] ?? $json['dataTerbaru'] ?? [];

            } catch (\Throwable $e) {
                return view('backend.dashboard.masyarakat', [
                    'user'         => $userArr,
                    'rekapStatus'  => [],
                    'totalLaporan' => 0,
                    'dataTerbaru'  => [],
                    'chartLabels'  => [],
                    'chartValues'  => [],
                    'apiError'     => 'Tidak dapat menghubungi server backend.',
                ]);
            }

            // chart dari rekap_tanggal
            $chartLabels = [];
            $chartValues = [];

            if (is_array($rekapTanggal)) {
                usort($rekapTanggal, function ($a, $b) {
                    return strcmp((string)($a['tanggal'] ?? ''), (string)($b['tanggal'] ?? ''));
                });

                foreach ($rekapTanggal as $row) {
                    $chartLabels[] = (string) ($row['tanggal'] ?? '');
                    $chartValues[] = (int) ($row['total'] ?? 0);
                }
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

        // =========================================================
        // ✅ DASHBOARD SUPERADMIN (Swagger Global)
        // GET /api/dashboard
        // View: backend.dashboard.superadmin
        // =========================================================
        if ($role === 'superadmin' || $role === 'super admin') {

            $rekapStatus  = [];
            $totalLaporan = 0;
            $rekapTanggal = [];
            $dataTerbaru  = [];

            try {
                $url  = $baseUrl . '/api/dashboard';
                $resp = Http::withToken($token)->acceptJson()->get($url);

                if ($resp->status() === 401) {
                    return redirect()->route('login')
                        ->withErrors(['_global' => 'Token expired. Silakan login ulang.']);
                }

                if (!$resp->ok()) {
                    return view('backend.dashboard.superadmin', [
                        'user'         => $userArr,
                        'rekapStatus'  => [],
                        'totalLaporan' => 0,
                        'dataTerbaru'  => [],
                        'chartLabels'  => [],
                        'chartValues'  => [],
                        'apiError'     => $resp->json('message')
                            ?? ('Gagal ambil dashboard superadmin (HTTP ' . $resp->status() . ')'),
                    ]);
                }

                $json = $resp->json() ?? [];

                $rekapStatus  = $json['rekap_status'] ?? [];
                $totalLaporan = (int) ($json['total'] ?? 0);
                $rekapTanggal = $json['rekap_tanggal'] ?? [];
                $dataTerbaru  = $json['data_terbaru'] ?? $json['dataTerbaru'] ?? [];

            } catch (\Throwable $e) {
                return view('backend.dashboard.superadmin', [
                    'user'         => $userArr,
                    'rekapStatus'  => [],
                    'totalLaporan' => 0,
                    'dataTerbaru'  => [],
                    'chartLabels'  => [],
                    'chartValues'  => [],
                    'apiError'     => 'Tidak dapat menghubungi server backend.',
                ]);
            }

            // chart dari rekap_tanggal
            $chartLabels = [];
            $chartValues = [];

            if (is_array($rekapTanggal)) {
                usort($rekapTanggal, function ($a, $b) {
                    return strcmp((string)($a['tanggal'] ?? ''), (string)($b['tanggal'] ?? ''));
                });

                foreach ($rekapTanggal as $row) {
                    $chartLabels[] = (string) ($row['tanggal'] ?? '');
                    $chartValues[] = (int) ($row['total'] ?? 0);
                }
            }

            return view('backend.dashboard.superadmin', [
                'user'         => $userArr,
                'rekapStatus'  => $rekapStatus,
                'totalLaporan' => $totalLaporan,
                'dataTerbaru'  => $dataTerbaru,
                'chartLabels'  => $chartLabels,
                'chartValues'  => $chartValues,
            ]);
        }

        // =========================================================
        // DEFAULT (role lain: upt/sda/dll) -> sementara kosong dulu
        // =========================================================
        return view('backend.dashboard.index', [
            'user'            => $userArr,
            'rekapStatus'     => [],
            'totalLaporan'    => 0,
            'kecamatanCounts' => [],
            'chartLabels'     => [],
            'chartValues'     => [],
        ]);
    }
}