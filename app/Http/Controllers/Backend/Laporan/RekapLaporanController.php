<?php

namespace App\Http\Controllers\Backend\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class RekapLaporanController extends Controller
{
    /**
     * Halaman Utama Rekap
     */
    public function index()
    {
        return view('backend.rekap.index');
    }

    /**
     * AJAX Handler untuk mengambil data dari API
     */
    public function getRekapData(Request $request)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $type = $request->filter_type; // 'status', 'tanggal', 'kecamatan'
        $endpoint = '';
        $params = $request->all();

        // Tentukan Endpoint API berdasarkan filter
        switch ($type) {
            case 'tanggal':
                $endpoint = '/api/rekap/tanggal';
                break;
            case 'kecamatan':
                $endpoint = '/api/rekap/kecamatan';
                break;
            default: // status / general
                $endpoint = '/api/rekap';
                break;
        }

        try {
            $response = Http::withToken($token)->get($baseUrl . $endpoint, $params);
            
            if ($response->ok()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'status' => false, 
                    'message' => 'Gagal mengambil data dari server API.'
                ], $response->status());
            }

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Server Error.'], 500);
        }
    }

    /**
     * Handle Export PDF
     */
    public function exportPdf(Request $request)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) return redirect()->route('login');

        $type = $request->filter_type;
        $endpoint = '';

        // Mapping ke Endpoint PDF API
        switch ($type) {
            case 'tanggal':
                $endpoint = '/api/rekap/per-tanggal/pdf';
                break;
            case 'kecamatan':
                $endpoint = '/api/rekap/by-kecamatan/pdf';
                break;
            default: // status
                $endpoint = '/api/rekap/status/pdf';
                break;
        }

        try {
            // Request ke API dan langsung stream download ke user
            $response = Http::withToken($token)->get($baseUrl . $endpoint, $request->all());

            if ($response->ok()) {
                $filename = 'rekap-laporan-' . time() . '.pdf';
                return response($response->body())
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            } else {
                return back()->with('error', 'Gagal generate PDF dari server API.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan koneksi.');
        }
    }
}