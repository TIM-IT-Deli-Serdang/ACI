<?php

namespace App\Http\Controllers\Backend\MyProfile;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
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

        return view('backend.my_profile.index', compact('user'));



    }


    /**
     * Kembalikan form edit (rendered HTML) via AJAX
     * Frontend lama mungkin memanggil ini via AJAX dan menaruh HTML ke modal.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function editAvatar($id): JsonResponse
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

        $html = view('backend.my_profile.change_pic', [
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
    public function updateAvatar(Request $request, $id = null): JsonResponse
{
    $baseUrl = rtrim(env('API_BASE_URL', ''), '/'); // misal: http://10.0.22.111
    $token   = Session::get('auth_token');

    if (!$token) {
        return response()->json([
            'error' => 'Anda belum login atau sesi telah berakhir.'
        ], 401);
    }

    // Pastikan id tersedia (dari route atau hidden input)
    if (empty($id)) {
        $id = $request->input('hidden_id') ?: null;
    }
    if (empty($id)) {
        return response()->json(['error' => 'User id tidak diberikan.'], 400);
    }

    $userAgent = $request->header('User-Agent', 'unknown');
    $clientIp  = $request->header('X-Real-IP')
                ?? $request->header('X-Forwarded-For')
                ?? $request->ip();

    // build URL API sesuai yang kamu sebutkan
    $url = $baseUrl . '/api/acount/' . $id . '/avatar';

    try {
        $client = Http::withHeaders([
                'User-Agent' => $userAgent,
                'X-Forwarded-For' => $clientIp,
                'X-Real-IP' => $clientIp,
            ])
            ->withToken($token)
            ->acceptJson();

        // Jika ada file avatar, attach; jika tidak ada, kirim request kosong ke backend
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            // attach otomatis handles multipart/form-data
            $client = $client->attach(
                'avatar',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            );

            // Biasanya backend menerima POST untuk upload; gunakan POST sesuai backend (kamu bisa ganti ke put jika perlu)
            $response = $client->post($url);
        } else {
            // Kirim request tanpa file — backend akan mengembalikan 422 dengan pesan validasinya
            $response = $client->post($url);
        }

    } catch (\Exception $e) {
        \Log::error("Update avatar failed: " . $e->getMessage());
        return response()->json([
            'error' => 'Tidak dapat menghubungi server backend.'
        ], 500);
    }

    // Jika token invalid / unauthorized
    if ($response->status() === 401) {
        Session::forget('auth_token');
        Session::forget('user');
        return response()->json([
            'error' => 'Token tidak valid atau sesi berakhir. Silakan login ulang.'
        ], 401);
    }

    // Forward validation errors dari backend (422) langsung ke frontend (JS)
    if ($response->status() === 422) {
        $errors = $response->json('errors') ?? $response->json();
        return response()->json(['errors' => $errors], 422);
    }

    // Jika backend mengembalikan error lain
    if (!$response->ok() && $response->status() !== 201) {
        $message = $response->json('message') ?? $response->json('error') ?? 'Terjadi kesalahan saat memperbarui avatar.';
        return response()->json(['error' => $message], $response->status());
    }

    // Berhasil -> ambil response body
    $resp = $response->json();

    // Backend kamu mengembalikan avatar_url di response body (lihat kode backend): asset('storage/user/avatar/...')
    // Sinkronisasi session user jika backend mengirim data user/field yang diupdate
    $updated = $resp['data'] ?? $resp['updated'] ?? $resp['user'] ?? null;

    if ($updated) {
        $current = Session::get('user', []);
        $merged = array_merge(is_array($current) ? $current : (array)$current, is_array($updated) ? $updated : (array)$updated);
        Session::put('user', $merged);
    } else {
        // jika backend hanya mengembalikan avatar_url di root response
        if (isset($resp['avatar_url'])) {
            $current = Session::get('user', []);
            $currentArr = is_array($current) ? $current : (array)$current;
            $currentArr['avatar_url'] = $resp['avatar_url'];
            // juga set 'avatar' jika kamu ingin menyimpan filename
            if (isset($resp['avatar'])) {
                $currentArr['avatar'] = $resp['avatar'];
            }
            Session::put('user', $currentArr);
            $updated = $currentArr;
        }
    }

    return response()->json([
        'success' => $resp['success'] ?? 'Avatar berhasil diperbarui.',
        'time'    => $resp['time'] ?? Carbon::now()->toDateTimeString(),
        'updated' => $updated,
        'avatar_url' => $resp['avatar_url'] ?? ($updated['avatar_url'] ?? null)
    ], $response->status() === 201 ? 201 : 200);
}

}
