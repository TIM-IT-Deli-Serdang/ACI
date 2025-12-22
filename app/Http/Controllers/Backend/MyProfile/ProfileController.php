<?php
    
namespace App\Http\Controllers\Backend\MyProfile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\JsonResponse;

    
class ProfileController extends Controller
{ 
   
    function __construct()
    {
        
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        // Jika tidak ada token -> redirect ke login
        if (!$token) {
            return redirect()->route('login')->withErrors([
                '_global' => 'Anda belum login atau sesi telah berakhir. Silakan login kembali.'
            ]);
        }

        // Forward User-Agent dan IP dari browser ke backend API (opsional tapi berguna)
        $userAgent = $request->header('User-Agent', 'unknown');
        $clientIp  = $request->header('X-Real-IP')
                    ?? $request->header('X-Forwarded-For')
                    ?? $request->ip();

        try {
            // Panggil endpoint backend untuk mendapatkan data user yang sedang login
            $response = Http::withHeaders([
                    'User-Agent' => $userAgent,
                    'X-Forwarded-For' => $clientIp,
                    'X-Real-IP' => $clientIp,
                ])
                ->withToken($token)
                ->acceptJson()
                ->get($baseUrl . '/api/me'); // sesuaikan endpoint jika berbeda
        } catch (\Exception $e) {
            // Gagal koneksi ke backend
            return redirect()->route('dashboard.index')->withErrors([
                '_global' => 'Tidak dapat menghubungi server backend.'
            ]);
        }

        // Jika unauthorized (token invalid / expired)
        if ($response->status() === 401) {
            // Hapus token dari session agar pengguna diarahkan login ulang
            Session::forget('auth_token');
            Session::forget('user');
            return redirect()->route('login')->withErrors([
                '_global' => 'Token tidak valid atau sudah expired. Silakan login kembali.'
            ]);
        }

        // Validation error dari backend
        if ($response->status() === 422) {
            return redirect()->route('dashboard.index')->withErrors([
                '_global' => $response->json('message') ?? 'Validation error pada backend.'
            ]);
        }

        // Error lain dari backend
        if (!$response->ok()) {
            return redirect()->route('dashboard.index')->withErrors([
                '_global' => $response->json('message') ?? 'Gagal mengambil data user.'
            ]);
        }

        // Ambil data user dari response
        $userData = $response->json('data') ?? $response->json()['data'] ?? null;

        if (!$userData) {
            // fallback jika struktur tidak seperti yang diharapkan
            return redirect()->route('dashboard.index')->withErrors([
                '_global' => 'Respons backend tidak memuat data user.'
            ]);
        }
        // Simpan ke session supaya blade lama yang memakai session('user') tetap bekerja
        Session::put('user', $userData);

        // Kirim ke view (bisa berupa array atau object; di blade gunakan optional() atau array access)
        // Saya kirim sebagai $user (array). Blade sebelumnya mengakses $user->avatar; jika ingin object:
        // $user = (object) $userData; dan compact('user')
        $user = (object) $userData;

        return view('backend.my_profile.profile.index', compact('user'));



    }

   

    /**
     * Kembalikan form edit (rendered HTML) via AJAX
     * Frontend lama mungkin memanggil ini via AJAX dan menaruh HTML ke modal.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function edit($id): JsonResponse
    {
        // Ambil user dari session (karena ini frontend controller)
        $userData = Session::get('user');

        // Jika id mismatch atau session kosong, ambil ulang dari API atau kirim error
        if (!$userData || (string)($userData['id'] ?? $userData->id ?? '') !== (string)$id) {
            // Fallback sederhana: coba ambil data session saja
            // (Anda bisa memperluas untuk fetch API by id jika perlu)
            return response()->json([
                'error' => 'Data user tidak ditemukan atau sesi tidak sesuai.'
            ], 404);
        }

        // Buat object model untuk view
        $user = is_object($userData) ? $userData : (object) $userData;

        $html = view('backend.my_profile.profile.edit', [
            'user' => $user
        ])->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Update profile via backend API PUT /api/me
     *
     * @param Request $request
     * @param int|string|null $id  (tidak digunakan pada API /api/me tapi tetap menerima untuk compatibility)
     * @return JsonResponse
     */
    public function update(Request $request, $id = null): JsonResponse
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) {
            return response()->json([
                'error' => 'Anda belum login atau sesi telah berakhir.'
            ], 401);
        }

        $payload = $request->only(['name', 'no_wa', 'email']);

        $userAgent = $request->header('User-Agent', 'unknown');
        $clientIp  = $request->header('X-Real-IP')
                    ?? $request->header('X-Forwarded-For')
                    ?? $request->ip();

        try {
            $response = Http::withHeaders([
                    'User-Agent' => $userAgent,
                    'X-Forwarded-For' => $clientIp,
                    'X-Real-IP' => $clientIp,
                ])
                ->withToken($token)
                ->acceptJson()
                ->put($baseUrl . '/api/me', $payload);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Tidak dapat menghubungi server backend.'
            ], 500);
        }

        // Token invalid / unauthorized
        if ($response->status() === 401) {
            Session::forget('auth_token');
            Session::forget('user');
            return response()->json([
                'error' => 'Token tidak valid atau sesi berakhir. Silakan login ulang.'
            ], 401);
        }

        // Validation errors forwarded dari backend
        if ($response->status() === 422) {
            // Pastikan format errors konsisten untuk frontend JS (object field -> array)
            $errors = $response->json('errors') ?? $response->json();
            return response()->json(['errors' => $errors], 422);
        }

        if (!$response->ok()) {
            $message = $response->json('message') ?? 'Terjadi kesalahan saat memperbarui profil.';
            return response()->json(['error' => $message], $response->status());
        }

        // Berhasil -> update session('user') agar UI langsung reflect perubahan
        $resp = $response->json();
        $updated = $resp['updated'] ?? $resp['data'] ?? ($resp['user'] ?? null);

        if ($updated) {
            // Jika backend mengembalikan 'user' atau 'updated' data, sinkronkan session user
            $current = Session::get('user', []);
            $merged = array_merge(is_array($current) ? $current : (array)$current, is_array($updated) ? $updated : (array)$updated);
            Session::put('user', $merged);
        } else {
            // Jika backend tidak mengembalikan updated fields, kita bisa re-fetch /api/me jika diperlukan.
            // Untuk efisiensi, abaikan jika tidak tersedia.
        }

        return response()->json([
            'success' => $resp['success'] ?? 'Data profile berhasil diperbaharui.',
            'time'    => $resp['time'] ?? Carbon::now()->diffForHumans(),
            'updated' => $updated
        ], 200);
    }

   
    
   
}