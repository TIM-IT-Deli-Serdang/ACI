<?php

namespace App\Http\Controllers\Backend\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LaporanWargaController extends Controller
{
    /**
     * Halaman Utama (List Laporan)
     */
    public function index(Request $request)
    {
        // Jika Anda ingin menampilkan Dashboard Stats (seperti di gambar)
        // Anda perlu mengambil datanya dari API di sini dan mengirimnya ke view
        // Contoh:
        // $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        // $token   = Session::get('auth_token');
        // $stats   = Http::withToken($token)->get($baseUrl . '/api/laporan/stats')->json();
        
        return view('backend.laporan.index'); 
    }

    /**
     * Data untuk DataTables (AJAX)
     */
    public function getData(Request $request)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) {
            return response()->json(['status' => false, 'message' => 'Anda belum login.'], 401);
        }

        try {
            // Forward parameter DataTables ke API Backend
            $response = Http::withToken($token)
                ->get($baseUrl . '/api/laporan/datatables', $request->all());
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Tidak dapat menghubungi server backend.'], 500);
        }

        if ($response->status() === 401) {
            return response()->json(['status' => false, 'message' => 'Sesi kedaluwarsa.'], 401);
        }

        if (!$response->ok()) {
            return response()->json([
                'status' => false, 
                'message' => $response->json()['message'] ?? 'Gagal mengambil data.'
            ], $response->status());
        }

        return response()->json($response->json());
    }

    /**
     * Simpan Laporan Baru
     */
    public function store(Request $request)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) {
            return response()->json(['status' => false, 'message' => 'Anda belum login.'], 401);
        }

        // 1. VALIDASI INPUT LOKAL (Gunakan nama field sesuai Form HTML / Blade)
        // Menggunakan 'wilayah_kecamatan_id' sesuai dropdown dependent
        $validator = Validator::make($request->all(), [
            'kategori_laporan_id'   => 'required|in:1,2,3,4,5',
            'deskripsi'             => 'required|string',
            'alamat'                => 'required|string',
            'wilayah_kecamatan_id'  => 'required', 
            'wilayah_kelurahan_id'  => 'required',
            
            'file_masyarakat' => [
                'nullable',
                'file',
                function ($attribute, $value, $fail) {
                    $imageExt = ['jpg', 'jpeg', 'png'];
                    $videoExt = ['mp4', 'mov', 'avi', 'mkv', 'webm'];
                    $ext = strtolower($value->getClientOriginalExtension());
                    $size = $value->getSize();

                    if (in_array($ext, $imageExt)) {
                        if ($size > 2097152) $fail('Ukuran gambar maksimal 2MB.');
                    } elseif (in_array($ext, $videoExt)) {
                        if ($size > 125829120) $fail('Ukuran video maksimal 120MB.');
                    } else {
                        $fail('Format file tidak valid.');
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => 'Validasi Gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // 2. PERSIAPAN DATA UNTUK API (MAPPING FIELD)
        // Kita buang field 'wilayah_...' dan buat field baru 'kecamatan_id' agar API menerima
        $dataToSend = $request->except(['file_masyarakat', 'wilayah_kecamatan_id', 'wilayah_kelurahan_id']);
        
        // [FIX] Mapping nama field: Form (wilayah_...) -> API (kecamatan_id)
        $dataToSend['kecamatan_id'] = $request->wilayah_kecamatan_id;
        $dataToSend['kelurahan_id'] = $request->wilayah_kelurahan_id;

        // 3. KIRIM KE API
        try {
            $http = Http::withToken($token)->timeout(300);

            if ($request->hasFile('file_masyarakat')) {
                $file = $request->file('file_masyarakat');
                $stream = fopen($file->getRealPath(), 'r');

                $http->attach(
                    'file_masyarakat',
                    $stream,
                    $file->getClientOriginalName()
                );
            }

            // Kirim $dataToSend yang sudah dimapping
            $response = $http->post($baseUrl . '/api/laporan', $dataToSend);

        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => 'Koneksi Error: ' . $e->getMessage()], 500);
        }

        // 4. HANDLE RESPONSE
        if ($response->status() === 401) {
            return response()->json(['status' => false, 'message' => 'Sesi kedaluwarsa.'], 401);
        }

        if ($response->status() === 422) {
            return response()->json([
                'status'  => 422,
                'message' => $response->json()['message'] ?? 'Validasi API Gagal',
                'errors'  => $response->json()['errors'] ?? []
            ], 422);
        }

        if (!$response->ok()) {
            return response()->json([
                'status' => false, 
                'message' => $response->json()['message'] ?? 'Gagal menyimpan data.'
            ], $response->status());
        }

        return response()->json($response->json(), $response->status());
    }

    /**
     * Tampilkan Detail (Modal Show)
     */
    public function show($id)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) return response()->json(['status' => false, 'message' => 'Login required.'], 401);

        try {
            $response = Http::withToken($token)->get($baseUrl . '/api/laporan/' . $id);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Server error.'], 500);
        }

        if (!$response->ok()) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan.'], $response->status());
        }

        // Ambil data dari response API
        $data = $response->json()['data'] ?? [];

        // Render view partial menjadi HTML string
        $html = view('backend.laporan.show', ['data' => $data])->render();

        return response()->json(['html' => $html], 200);
    }

    /**
     * Tampilkan Form Edit (Modal Edit)
     */
    public function edit($id)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) return response()->json(['status' => false, 'message' => 'Login required.'], 401);

        try {
            $response = Http::withToken($token)->get($baseUrl . '/api/laporan/' . $id);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Server error.'], 500);
        }

        if (!$response->ok()) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan.'], $response->status());
        }

        $data = $response->json()['data'] ?? [];

        // Render view edit menjadi HTML string
        $html = view('backend.laporan.edit', ['data' => $data])->render();
        
        return response()->json(['html' => $html], 200);
    }

    /**
     * Update Data Laporan
     */
    public function update(Request $request, $id)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) {
            return response()->json(['status' => false, 'message' => 'Anda belum login.'], 401);
        }

        // 1. VALIDASI INPUT (Gunakan nama field sesuai Form HTML: wilayah_...)
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'kategori_laporan_id'   => 'required|in:1,2,3,4,5',
            'deskripsi'             => 'required|string',
            'alamat'                => 'required|string',
            'wilayah_kecamatan_id'  => 'required', // Nama sesuai form
            'wilayah_kelurahan_id'  => 'required', // Nama sesuai form
            
            'file_masyarakat' => [
                'nullable',
                'file',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    $size = $value->getSize();
                    if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                        if ($size > 2097152) $fail('Ukuran gambar maksimal 2MB.');
                    } elseif (in_array($ext, ['mp4', 'mov', 'avi', 'mkv', 'webm'])) {
                        if ($size > 125829120) $fail('Ukuran video maksimal 120MB.');
                    } else {
                        $fail('Format file tidak valid.');
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => 'Validasi Gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // 2. MAPPING DATA (Form -> API)
        // Hapus field lama
        $dataToSend = $request->except(['file_masyarakat', '_method', 'wilayah_kecamatan_id', 'wilayah_kelurahan_id']);
        
        // Buat field baru sesuai permintaan API Backend
        $dataToSend['kecamatan_id'] = $request->wilayah_kecamatan_id;
        $dataToSend['kelurahan_id'] = $request->wilayah_kelurahan_id;

        try {
            $http = Http::withToken($token)->timeout(300);

            // 3. ATTACH FILE JIKA ADA
            if ($request->hasFile('file_masyarakat')) {
                $file = $request->file('file_masyarakat');
                $stream = fopen($file->getRealPath(), 'r');
                
                $http->attach(
                    'file_masyarakat',
                    $stream,
                    $file->getClientOriginalName()
                );
            }

            // 4. KIRIM DATA YANG SUDAH DI-MAPPING
            $response = $http->post($baseUrl . '/api/laporan/' . $id, $dataToSend);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => 'Connection Error: ' . $e->getMessage()], 500);
        }

        if (!$response->ok()) {
            return response()->json([
                'status' => false, 
                'message' => $response->json()['message'] ?? 'Gagal update data.'
            ], $response->status());
        }

        return response()->json($response->json(), $response->status());
    }

    /**
     * Hapus Data
     */
    public function destroy($id)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) return response()->json(['status' => false, 'message' => 'Login required.'], 401);

        try {
            $response = Http::withToken($token)->delete($baseUrl . '/api/laporan/' . $id);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Server error.'], 500);
        }

        if (!$response->ok()) {
            return response()->json([
                'status' => false, 
                'message' => $response->json()['message'] ?? 'Gagal menghapus data.'
            ], $response->status());
        }

        return response()->json($response->json(), $response->status());
    }

    /**
     * Halaman Validasi Laporan
     */
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

        if (!$respLaporan->ok()) return back()->with('error', 'Gagal mengambil data laporan.');
        
        $dataLaporan = $respLaporan->json()['data'] ?? [];

        // 2. Cek Role User & Ambil UPT jika perlu
        $sessionUser = Session::get('user');
        $userId = is_array($sessionUser) ? $sessionUser['id'] : $sessionUser->id;
        
        // Cek Role via API Users
        $respUser = Http::withToken($token)->get($baseUrl . '/api/users/' . $userId);
        $roleNames = [];
        if ($respUser->ok()) {
            $userData = $respUser->json()['data'] ?? [];
            if (isset($userData['roles'])) {
                $roleNames = collect($userData['roles'])->pluck('name')->toArray();
            }
        }

        $isSuperAdmin = in_array('Superadmin', $roleNames);
        $isSda        = in_array('sda', $roleNames);
        $isUpt        = in_array('upt', $roleNames);

        // Ambil List UPT jika status 0 atau sudah ada UPT
        $listUpt = [];
        $shouldFetchUpt = ($dataLaporan['status_laporan'] == 0 && ($isSuperAdmin || $isSda))
            || !empty($dataLaporan['upt_id']);

        if ($shouldFetchUpt) {
            try {
                $respUpt = Http::withToken($token)->get($baseUrl . '/api/upt', ['length' => 1000]);
                if ($respUpt->ok()) $listUpt = $respUpt->json()['data'] ?? [];
            } catch (\Exception $e) {}
        }

        return view('backend.laporan.validation', [
            'data'         => $dataLaporan,
            'isSuperAdmin' => $isSuperAdmin,
            'isSda'        => $isSda,
            'isUpt'        => $isUpt,
            'listUpt'      => $listUpt,
        ]);
    }

    /**
     * Proses Validasi (Next/Reject)
     */
    public function processValidation(Request $request, $id)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) return redirect()->route('login');

        $action = $request->action;
        $currentStatus = (int) $request->current_status;
        $keterangan = $request->keterangan;

        // 1. Validasi Input (File Verifikasi Wajib jika Status 1 -> 2)
        if ($currentStatus == 1 && $action === 'next') {
            $validator = Validator::make($request->all(), [
                'keterangan' => 'required|string',
                'verif_file' => [
                    'required',
                    'file',
                    function ($attribute, $value, $fail) {
                        $allowedExt = ['jpg', 'jpeg', 'png', 'mp4', 'mov', 'pdf'];
                        $ext = strtolower($value->getClientOriginalExtension());
                        $size = $value->getSize();

                        if (!in_array($ext, $allowedExt)) $fail('Format file tidak didukung.');
                        if (in_array($ext, ['jpg','jpeg','png','pdf']) && $size > 2097152) $fail('Max 2MB.');
                        if (in_array($ext, ['mp4','mov']) && $size > 125829120) $fail('Max 120MB.');
                    }
                ],
            ]);

            if ($validator->fails()) return back()->with('error', $validator->errors()->first())->withInput();
        }

        // 2. Siapkan Payload
        $payload = [];
        if ($action === 'reject') {
            $payload['status_laporan'] = 5;
            // Mapping keterangan tolak sesuai status
            if ($currentStatus == 0) $payload['penerima_keterangan_tolak'] = $keterangan;
            elseif ($currentStatus == 1) $payload['verif_keterangan_tolak'] = $keterangan;
            elseif ($currentStatus == 2) $payload['penanganan_keterangan_tolak'] = $keterangan;
            elseif ($currentStatus == 3) $payload['selesai_keterangan_tolak'] = $keterangan;
        } else {
            // Next Step
            if ($currentStatus == 0) {
                $payload['status_laporan'] = 1;
                $payload['penerima_keterangan'] = $keterangan;
                $payload['upt_id'] = $request->upt_id;
            } elseif ($currentStatus == 1) {
                $payload['status_laporan'] = 2;
                $payload['verif_keterangan'] = $keterangan;
            }
            elseif ($currentStatus == 2) {
                $payload['status_laporan'] = 3;
                $payload['penanganan_keterangan'] = $keterangan;
            } elseif ($currentStatus == 3) {
                $payload['status_laporan'] = 4;
                $payload['selesai_keterangan'] = $keterangan;
            }
        }

        // 3. Kirim API
        try {
            $http = Http::withToken($token)->timeout(300);

            // Upload File Verifikasi
            if ($currentStatus == 1 && $action === 'next' && $request->hasFile('verif_file')) {
                $file = $request->file('verif_file');
                $stream = fopen($file->getRealPath(), 'r');
                
                $http->attach('verif_file', $stream, $file->getClientOriginalName());
            }

            $response = $http->post($baseUrl . '/api/laporan/update-status/' . $id, $payload);

            if ($response->successful()) {
                return redirect()->route('laporan.validation', $id)->with('success', 'Status laporan berhasil diperbarui.');
            } else {
                return back()->with('error', $response->json()['message'] ?? 'Gagal update status.')->withInput();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Server error: ' . $e->getMessage());
        }
    }
}   
