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
   



    // public function store(Request $request)
    // {
    //     $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
    //     $token   = Session::get('auth_token');

    //     if (!$token) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Anda belum login.'
    //         ], 401);
    //     }

    //     try {
    //         // Inisialisasi HTTP Client dengan Token
    //         $http = Http::withToken($token);

    //         // --- PERBAIKAN DI SINI: ATTACH FILE ---
    //         if ($request->hasFile('file_masyarakat')) {
    //             $file = $request->file('file_masyarakat');
                
    //             // Lampirkan file ke request
    //             // Parameter: (nama_field, isi_file, nama_asli_file)
    //             $http->attach(
    //                 'file_masyarakat', 
    //                 file_get_contents($file->getRealPath()), 
    //                 $file->getClientOriginalName()
    //             );
    //         }

    //         // Kirim sisa data (text inputs) menggunakan POST
    //         // Kita gunakan $request->except('file_masyarakat') agar file tidak dikirim double (sekali via attach, sekali via post body)
    //         $response = $http->post($baseUrl . '/api/laporan', $request->except('file_masyarakat'));

    //     } catch (\Throwable $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Tidak dapat menghubungi server backend: ' . $e->getMessage()
    //         ], 500);
    //     }

    //     // ... (Sisa kode validasi response sama seperti sebelumnya) ...
        
    //     if ($response->status() === 401) {
    //         return response()->json(['status' => false, 'message' => 'Token expired.'], 401);
    //     }

    //     if ($response->status() === 422) {
    //         return response()->json([
    //             'status'  => 422,
    //             'message' => $response->json()['message'] ?? 'Validation Error',
    //             'errors'  => $response->json()['errors'] ?? []
    //         ], 422);
    //     }

    //     if (!$response->ok()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $response->json()['message'] ?? 'Gagal menyimpan data.'
    //         ], $response->status());
    //     }

    //     return response()->json($response->json(), $response->status());
    // }

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

        // 1. VALIDASI INPUT (Validasi Ukuran File Gambar vs Video)
        // Kita validasi di sini dulu agar tidak membuang bandwidth upload ke API jika file salah
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'kategori_laporan_id' => 'required|in:1,2,3,4,5',
            'deskripsi'           => 'required|string',
            'alamat'              => 'required|string',
            'kecamatan_id'        => 'required', // sesuaikan validasi tipe datanya
            'kelurahan_id'        => 'required',
            
            // Validasi File Custom Closure
            'file_masyarakat' => [
                'nullable',
                'file',
                function ($attribute, $value, $fail) {
                    $imageExt = ['jpg', 'jpeg', 'png'];
                    $videoExt = ['mp4', 'mov', 'avi', 'mkv', 'webm'];

                    $ext = strtolower($value->getClientOriginalExtension());
                    $size = $value->getSize(); // dalam bytes

                    if (in_array($ext, $imageExt)) {
                        // Maksimal 2MB untuk gambar (2 * 1024 * 1024)
                        if ($size > 2097152) { 
                            $fail('Ukuran gambar maksimal 2MB.');
                        }
                    } elseif (in_array($ext, $videoExt)) {
                        // Maksimal 120MB untuk video (120 * 1024 * 1024)
                        if ($size > 125829120) { 
                            $fail('Ukuran video maksimal 120MB.');
                        }
                    } else {
                        $fail('File harus berupa gambar (jpg, png) atau video (mp4, mov, dsb).');
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

        // 2. KIRIM KE API BACKEND
        try {
            // Set timeout lebih lama (misal 300 detik / 5 menit) untuk upload video
            $http = Http::withToken($token)->timeout(300);

            // Attach File jika ada
            if ($request->hasFile('file_masyarakat')) {
                $file = $request->file('file_masyarakat');
                
                // Gunakan fopen agar hemat memori saat upload file besar (stream)
                $stream = fopen($file->getRealPath(), 'r');
                
                $http->attach(
                    'file_masyarakat', 
                    $stream, 
                    $file->getClientOriginalName()
                );
            }

            // Kirim data text lainnya (kecuali file karena sudah di-attach)
            // Tambahkan _method POST explicit jika perlu, tapi default post() sudah POST
            $response = $http->post($baseUrl . '/api/laporan', $request->except('file_masyarakat'));

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghubungi server (Timeout/Connection Error): ' . $e->getMessage()
            ], 500);
        }

        // 3. HANDLE RESPONSE DARI API
        if ($response->status() === 401) {
            return response()->json(['status' => false, 'message' => 'Sesi Anda telah berakhir.'], 401);
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
                'message' => $response->json()['message'] ?? 'Terjadi kesalahan saat menyimpan data.'
            ], $response->status());
        }

        // Berhasil
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


    // public function update(Request $request, $id)
    // {
    //     $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
    //     $token   = Session::get('auth_token');

    //     if (!$token) {
    //         return response()->json(['status' => false, 'message' => 'Anda belum login.'], 401);
    //     }

    //     try {
    //         $http = Http::withToken($token);

    //         // 1. ATTACH FILE JIKA ADA
    //         if ($request->hasFile('file_masyarakat')) {
    //             $file = $request->file('file_masyarakat');
    //             $http->attach(
    //                 'file_masyarakat', 
    //                 file_get_contents($file->getRealPath()), 
    //                 $file->getClientOriginalName()
    //             );
    //         }

    //         // 2. KIRIM SEBAGAI POST (Sesuai Swagger Backend)
    //         // Kita buang field '_method' agar backend tidak menganggapnya sebagai PUT
    //         // Kita buang 'file_masyarakat' dari body karena sudah di-attach
    //         $dataToSend = $request->except(['file_masyarakat', '_method']);

    //         $response = $http->post($baseUrl . '/api/laporan/' . $id, $dataToSend);

    //     } catch (\Throwable $e) {
    //         return response()->json(['status' => false, 'message' => 'Tidak dapat menghubungi server backend: ' . $e->getMessage()], 500);
    //     }

    //     // --- VALIDASI RESPONSE ---

    //     if ($response->status() === 401) {
    //         return response()->json(['status' => false, 'message' => 'Token tidak valid atau sudah expired.'], 401);
    //     }

    //     if ($response->status() === 422) {
    //         return response()->json([
    //             'status'  => 422,
    //             'message' => $response->json()['message'] ?? 'Validation Error',
    //             'errors'  => $response->json()['errors'] ?? []
    //         ], 422);
    //     }

    //     if (!$response->ok()) {
    //         return response()->json(['status' => false, 'message' => $response->json()['message'] ?? 'Gagal memperbarui data.'], $response->status());
    //     }

    //     // Berhasil -> teruskan body API dan status
    //     return response()->json($response->json(), $response->status());
    // }
    public function update(Request $request, $id)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) {
            return response()->json(['status' => false, 'message' => 'Anda belum login.'], 401);
        }

        // 1. VALIDASI INPUT (Sama seperti Store: Gambar 2MB, Video 120MB)
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'kategori_laporan_id' => 'required|in:1,2,3,4,5',
            'deskripsi'           => 'required|string',
            'alamat'              => 'required|string',
            'kecamatan_id'        => 'required',
            'kelurahan_id'        => 'required',
            
            // Validasi File Custom Closure
            'file_masyarakat' => [
                'nullable',
                'file',
                function ($attribute, $value, $fail) {
                    $imageExt = ['jpg', 'jpeg', 'png'];
                    $videoExt = ['mp4', 'mov', 'avi', 'mkv', 'webm'];

                    $ext = strtolower($value->getClientOriginalExtension());
                    $size = $value->getSize();

                    if (in_array($ext, $imageExt)) {
                        if ($size > 2097152) { // 2MB
                            $fail('Ukuran gambar maksimal 2MB.');
                        }
                    } elseif (in_array($ext, $videoExt)) {
                        if ($size > 125829120) { // 120MB
                            $fail('Ukuran video maksimal 120MB.');
                        }
                    } else {
                        $fail('File harus berupa gambar (jpg, png) atau video (mp4, mov, dsb).');
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

        try {
            // Set timeout panjang untuk video
            $http = Http::withToken($token)->timeout(300);

            // 2. ATTACH FILE JIKA ADA
            if ($request->hasFile('file_masyarakat')) {
                $file = $request->file('file_masyarakat');
                
                // Gunakan stream
                $stream = fopen($file->getRealPath(), 'r');

                $http->attach(
                    'file_masyarakat', 
                    $stream, 
                    $file->getClientOriginalName()
                );
            }

            // 3. KIRIM SEBAGAI POST (Sesuai Swagger Backend)
            // Buang '_method' karena backend API menerima POST murni untuk update ini
            $dataToSend = $request->except(['file_masyarakat', '_method']);

            $response = $http->post($baseUrl . '/api/laporan/' . $id, $dataToSend);

        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => 'Gagal menghubungi server (Timeout/Connection): ' . $e->getMessage()], 500);
        }

        // --- HANDLE RESPONSE ---
        if ($response->status() === 401) {
            return response()->json(['status' => false, 'message' => 'Token tidak valid atau sudah expired.'], 401);
        }

        if ($response->status() === 422) {
            return response()->json([
                'status'  => 422,
                'message' => $response->json()['message'] ?? 'Validasi Error',
                'errors'  => $response->json()['errors'] ?? []
            ], 422);
        }

        if (!$response->ok()) {
            return response()->json(['status' => false, 'message' => $response->json()['message'] ?? 'Gagal memperbarui data.'], $response->status());
        }

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

        // ==========================================
        // 1. VALIDASI KHUSUS TAHAP VERIFIKASI (1 -> 2)
        // ==========================================
        if ($currentStatus == 1 && $action === 'next') {
            
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'keterangan' => 'required|string',
                // VALIDASI FILE CUSTOM (GAMBAR vs VIDEO)
                'verif_file' => [
                    'required', // Wajib ada file bukti saat verifikasi disetujui
                    'file',
                    function ($attribute, $value, $fail) {
                        $imageExt = ['jpg', 'jpeg', 'png'];
                        $videoExt = ['mp4', 'mov', 'avi', 'mkv', 'webm'];
                        $pdfExt   = ['pdf']; 

                        $ext = strtolower($value->getClientOriginalExtension());
                        $size = $value->getSize();

                        if (in_array($ext, $imageExt)) {
                            // Gambar Max 2MB
                            if ($size > 2097152) $fail('Ukuran gambar maksimal 2MB.');
                        } elseif (in_array($ext, $videoExt)) {
                            // Video Max 120MB
                            if ($size > 125829120) $fail('Ukuran video maksimal 120MB.');
                        } elseif (in_array($ext, $pdfExt)) {
                            // PDF Max 2MB
                            if ($size > 2097152) $fail('Ukuran PDF maksimal 2MB.');
                        } else {
                            $fail('File harus berupa Gambar, PDF, atau Video yang valid.');
                        }
                    }
                ],
            ]);

            if ($validator->fails()) {
                return back()->with('error', $validator->errors()->first())->withInput();
            }
        }

        // ==========================================
        // 2. SIAPKAN PAYLOAD STATUS
        // ==========================================
        $payload = [];
        
        if ($action === 'reject') {
            $payload['status_laporan'] = 5;
            if ($currentStatus == 0) $payload['penerima_keterangan_tolak'] = $keterangan;
            elseif ($currentStatus == 1) $payload['verif_keterangan_tolak'] = $keterangan;
            elseif ($currentStatus == 2) $payload['penanganan_keterangan_tolak'] = $keterangan;
            elseif ($currentStatus == 3) $payload['selesai_keterangan_tolak'] = $keterangan;

        } else {
            if ($currentStatus == 0) {
                $payload['status_laporan'] = 1;
                $payload['penerima_keterangan'] = $keterangan;
                $payload['upt_id'] = $request->upt_id; 
            } 
            elseif ($currentStatus == 1) {
                $payload['status_laporan'] = 2;
                $payload['verif_keterangan'] = $keterangan;
                // File dikirim via attach di bawah
            }
            elseif ($currentStatus == 2) {
                $payload['status_laporan'] = 3;
                $payload['penanganan_keterangan'] = $keterangan;
            }
            elseif ($currentStatus == 3) {
                $payload['status_laporan'] = 4;
                $payload['selesai_keterangan'] = $keterangan;
            }
        }

        // ==========================================
        // 3. KIRIM KE API
        // ==========================================
        try {
            // Set timeout 5 menit (300 detik) agar upload video tidak putus
            $http = Http::withToken($token)->timeout(300);

            // Upload File Verifikasi (Khusus Status 1 -> 2)
            if ($currentStatus == 1 && $action === 'next' && $request->hasFile('verif_file')) {
                $file = $request->file('verif_file');
                
                // Gunakan stream fopen agar hemat memori saat upload file besar
                $stream = fopen($file->getRealPath(), 'r');

                $http->attach(
                    'verif_file', 
                    $stream, 
                    $file->getClientOriginalName()
                );
            }

            $response = $http->post($baseUrl . '/api/laporan/update-status/' . $id, $payload);

            if ($response->successful()) {
                return redirect()->route('laporan.validation', $id)->with('success', 'Status laporan berhasil diperbarui.');
            } else {
                $msg = $response->json()['message'] ?? 'Gagal memproses validasi.';
                // Jika error validasi dari backend (422), tampilkan detailnya
                if ($response->status() === 422 && isset($response->json()['errors'])) {
                    $errors = $response->json()['errors'];
                    $firstError = reset($errors)[0] ?? $msg;
                    return back()->with('error', $firstError)->withInput();
                }
                return back()->with('error', $msg)->withInput();
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

}
