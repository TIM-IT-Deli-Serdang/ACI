<?php

namespace App\Http\Controllers\Backend\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class LaporanWargaController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('backend.laporan.index');
    }


public function getData(Request $request)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Anda belum login.'
            ], 401);
        }

        try {
            // forward parameter datatables (draw, start, length, search)
            $response = Http::withToken($token)
                ->get($baseUrl . '/api/laporan/datatables', $request->all());
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak dapat menghubungi server backend.'
            ], 500);
        }

        // backend error: unauthorized
        if ($response->status() === 401) {
            return response()->json([
                'status' => false,
                'message' => 'Token tidak valid atau sudah expired.'
            ], 401);
        }

        // backend validation
        if ($response->status() === 422) {
            return response()->json([
                'status' => false,
                'message' => $response->json()['message'] ?? 'Validation error',
                'errors' => $response->json()['errors'] ?? []
            ], 422);
        }

        // backend error lainnya
        if (!$response->ok()) {
            return response()->json([
                'status' => false,
                'message' => $response->json()['message'] ?? 'Gagal mengambil data Laporan.'
            ], $response->status());
        }

        // RETURN DATA KE DATATABLES
        return response()->json($response->json());
    }
   



    public function store(Request $request)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Anda belum login.'
            ], 401);
        }

        try {
            // Forward semua input ke API. Jika butuh hanya beberapa field, filter di sini.
            $response = Http::withToken($token)
                ->post($baseUrl . '/api/laporan', $request->all());
        } catch (\Throwable $e) {
            // Gagal koneksi ke backend
            return response()->json([
                'status' => false,
                'message' => 'Tidak dapat menghubungi server backend.'
            ], 500);
        }

        // Jika API mengembalikan Unauthorized
        if ($response->status() === 401) {
            return response()->json([
                'status' => false,
                'message' => 'Token tidak valid atau sudah expired.'
            ], 401);
        }

        // Jika API mengembalikan validation error
        if ($response->status() === 422) {
            // pastikan format errors diteruskan persis seperti API
            return response()->json([
                'status'  => 422,
                'message' => $response->json()['message'] ?? 'Validation Error',
                'errors'  => $response->json()['errors'] ?? []
            ], 422);
        }

        // Jika ada error lain dari API
        if (!$response->ok()) {
            return response()->json([
                'status' => false,
                'message' => $response->json()['message'] ?? 'Gagal menyimpan data Laporan.'
            ], $response->status());
        }

        // Berhasil (mis. 201 Created) -> teruskan body API ke frontend beserta status code
        return response()->json($response->json(), $response->status());
    }


    public function show($id)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Anda belum login.'
            ], 401);
        }

        try {
            $response = Http::withToken($token)
                ->get($baseUrl . '/api/laporan/' . $id);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak dapat menghubungi server backend.'
            ], 500);
        }

        if ($response->status() === 401) {
            return response()->json([
                'status' => false,
                'message' => 'Token tidak valid atau sudah expired.'
            ], 401);
        }

        if ($response->status() === 404) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        if (!$response->ok()) {
            return response()->json([
                'status' => false,
                'message' => $response->json()['message'] ?? 'Gagal mengambil data.'
            ], $response->status());
        }

        // misal API mengembalikan detail di root atau di 'data'
        $data = $response->json();
        if (isset($data['data'])) {
            $data = $data['data'];
        }

        // render view partial (blade) dan kirim sebagai html
        $html = view('backend.laporan.show', ['data' => $data])->render();

        return response()->json(['html' => $html], 200);
    }



    public function edit($id)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) {
            return response()->json(['status' => false, 'message' => 'Anda belum login.'], 401);
        }

        try {
            $response = Http::withToken($token)->get($baseUrl . '/api/laporan/' . $id);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Tidak dapat menghubungi server backend.'], 500);
        }

        if ($response->status() === 401) {
            return response()->json(['status' => false, 'message' => 'Token tidak valid atau sudah expired.'], 401);
        }

        if ($response->status() === 404) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        if (!$response->ok()) {
            return response()->json(['status' => false, 'message' => $response->json()['message'] ?? 'Gagal mengambil data.'], $response->status());
        }

        $data = $response->json();
        if (isset($data['data'])) $data = $data['data'];

        $html = view('backend.laporan.edit', ['data' => $data])->render();
        return response()->json(['html' => $html], 200);
    }


    public function update(Request $request, $id)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) {
            return response()->json(['status' => false, 'message' => 'Anda belum login.'], 401);
        }

        try {
            // Jika butuh file upload, gunakan multipart/form-data. Berikut asumsinya form-data biasa.
            $response = Http::withToken($token)
                ->put($baseUrl . '/api/laporan/' . $id, $request->all());
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => 'Tidak dapat menghubungi server backend.'], 500);
        }

        if ($response->status() === 401) {
            return response()->json(['status' => false, 'message' => 'Token tidak valid atau sudah expired.'], 401);
        }

        if ($response->status() === 422) {
            return response()->json([
                'status'  => 422,
                'message' => $response->json()['message'] ?? 'Validation Error',
                'errors'  => $response->json()['errors'] ?? []
            ], 422);
        }

        if (!$response->ok()) {
            return response()->json(['status' => false, 'message' => $response->json()['message'] ?? 'Gagal memperbarui data.'], $response->status());
        }

        // Berhasil -> teruskan body API (mis. message) dan status
        return response()->json($response->json(), $response->status());
    }


    public function destroy($id)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) {
            return response()->json(['status' => false, 'message' => 'Anda belum login.'], 401);
        }

        try {
            $response = Http::withToken($token)->delete($baseUrl . '/api/laporan/' . $id);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Tidak dapat menghubungi server backend.'], 500);
        }

        if ($response->status() === 401) {
            return response()->json(['status' => false, 'message' => 'Token tidak valid atau sudah expired.'], 401);
        }

        if ($response->status() === 404) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        if (!$response->ok()) {
            return response()->json([
                'status' => false,
                'message' => $response->json()['message'] ?? 'Gagal menghapus data.'
            ], $response->status());
        }

        // sukses -> teruskan response dari API (mis. message)
        return response()->json($response->json(), $response->status());
    }

    public function validation($id)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) return redirect()->route('login');

        // 1. Ambil Detail Laporan
        try {
            $respLaporan = Http::withToken($token)->get($baseUrl . '/api/laporan/' . $id);
        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi API terputus.');
        }

        if (!$respLaporan->ok()) {
            return back()->with('error', 'Gagal mengambil data laporan.');
        }
        $dataLaporan = $respLaporan->json()['data'] ?? [];

        // 2. Cek Role User
        $sessionUser = Session::get('user');
        if (!$sessionUser) return redirect()->route('login');
        
        $userId = is_array($sessionUser) ? $sessionUser['id'] : $sessionUser->id;
        $respUser = Http::withToken($token)->get($baseUrl . '/api/users/' . $userId);
        
        $roleNames = [];
        if ($respUser->ok()) {
            $userData = $respUser->json()['data'] ?? [];
            if (isset($userData['roles']) && is_array($userData['roles'])) {
                $roleNames = collect($userData['roles'])->pluck('name')->toArray();
            }
        }

        $isSuperAdmin = in_array('Superadmin', $roleNames);
        $isSda        = in_array('sda', $roleNames);
        $isUpt        = in_array('upt', $roleNames);

        // 3. [UPDATE] Ambil List UPT
        // Ambil jika Status = 0 (untuk dropdown) ATAU jika upt_id sudah terisi (untuk tampilan riwayat)
        $listUpt = [];
        $shouldFetchUpt = ($dataLaporan['status_laporan'] == 0 && ($isSuperAdmin || $isSda)) 
                          || !empty($dataLaporan['upt_id']);

        if ($shouldFetchUpt) {
            try {
                // Gunakan endpoint DataTables dengan length besar agar dapat semua
                $respUpt = Http::withToken($token)->get($baseUrl . '/api/upt', [
                    'length' => 1000, 
                    'start'  => 0
                ]);

                if ($respUpt->ok()) {
                    $jsonUpt = $respUpt->json();
                    $listUpt = $jsonUpt['data'] ?? []; 
                }
            } catch (\Exception $e) {
                // Silent fail
            }
        }

        return view('backend.laporan.validation', [
            'data'         => $dataLaporan,
            'isSuperAdmin' => $isSuperAdmin,
            'isSda'        => $isSda,
            'isUpt'        => $isUpt,
            'listUpt'      => $listUpt, // Kirim ke view
        ]);
    }

    public function processValidation(Request $request, $id)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');
        
        if (!$token) return redirect()->route('login');

        $action = $request->action; 
        $currentStatus = (int) $request->current_status;
        $keterangan = $request->keterangan;

        $payload = [];
        
        // --- LOGIKA MAPPING STATUS & FIELD ---
        if ($action === 'reject') {
            // Case Tolak
            $payload['status_laporan'] = 5;
            if ($currentStatus == 0) $payload['penerima_keterangan_tolak'] = $keterangan;
            elseif ($currentStatus == 1) $payload['verif_keterangan_tolak'] = $keterangan;
            elseif ($currentStatus == 2) $payload['penanganan_keterangan_tolak'] = $keterangan;
            elseif ($currentStatus == 3) $payload['selesai_keterangan_tolak'] = $keterangan;

        } else {
            // Case Terima / Lanjut
            if ($currentStatus == 0) {
                // 0 (Pengajuan) -> 1 (Diterima)
                $payload['status_laporan'] = 1;
                $payload['penerima_keterangan'] = $keterangan;
                
                // [BARU] Tambahkan Assign UPT ID
                $payload['upt_id'] = $request->upt_id; 
            } 
            elseif ($currentStatus == 1) {
                // 1 (Diterima) -> 2 (Verifikasi)
                $payload['status_laporan'] = 2;
                $payload['verif_keterangan'] = $keterangan;
            }
            elseif ($currentStatus == 2) {
                // 2 (Verifikasi) -> 3 (Penanganan)
                $payload['status_laporan'] = 3;
                $payload['penanganan_keterangan'] = $keterangan;
            }
            elseif ($currentStatus == 3) {
                // 3 (Penanganan) -> 4 (Selesai)
                $payload['status_laporan'] = 4;
                $payload['selesai_keterangan'] = $keterangan;
            }
        }

        try {
            $http = Http::withToken($token);

            // Upload File (Khusus Status 1 -> 2)
            if ($currentStatus == 1 && $action === 'next' && $request->hasFile('verif_file')) {
                $file = $request->file('verif_file');
                $http->attach('verif_file', file_get_contents($file), $file->getClientOriginalName());
            }

            // Kirim Request
            $response = $http->post($baseUrl . '/api/laporan/update-status/' . $id, $payload);

            if ($response->successful()) {
                return redirect()->route('laporan.validation', $id)->with('success', 'Status laporan berhasil diperbarui.');
            } else {
                $msg = $response->json()['message'] ?? 'Gagal memproses validasi.';
                return back()->with('error', $msg)->withInput();
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

}
