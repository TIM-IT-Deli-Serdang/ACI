<?php

namespace App\Http\Controllers\Backend\MyProfile;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        // 1. Cek Token
        if (!$token) {
            return redirect()->route('login')->withErrors([
                '_global' => 'Anda belum login atau sesi telah berakhir. Silakan login kembali.'
            ]);
        }

        // 2. Setup Headers (User Agent & IP)
        $headers = [
            'User-Agent'      => $request->header('User-Agent', 'unknown'),
            'X-Forwarded-For' => $request->ip(),
        ];

        try {
            // 3. Request ke API Backend
            $response = Http::withHeaders($headers)
                ->withToken($token)
                ->get($baseUrl . '/api/me');

            // 4. Handle Response Sukses (200 OK)
            if ($response->ok()) {
                $result = $response->json();

                // [PENTING] Ambil isi key 'data'. 
                // Struktur API Anda: { "status": true, "data": { ... } }
                $userData = $result['data'] ?? $result['user'] ?? null;

                if (!$userData) {
                    return back()->withErrors(['_global' => 'Data user kosong dari server API.']);
                }

                // 5. Simpan ke Session (Agar data user di session selalu fresh)
                Session::put('user', $userData);

                // 6. Return View
                // Kita kirim sebagai ARRAY (jangan di-cast ke object) agar cocok dengan Blade $user['roles']
                return view('backend.myprofile.account.index', [
                    'user' => $userData
                ]);
            }

            // 7. Handle 401 (Unauthorized - Token Expired)
            if ($response->status() === 401) {
                Session::forget(['auth_token', 'user']);
                return redirect()->route('login')->withErrors([
                    '_global' => 'Sesi telah berakhir. Silakan login kembali.'
                ]);
            }

            // 8. Handle Error Lainnya (422, 500, dll)
            return back()->withErrors([
                '_global' => $response->json('message') ?? 'Gagal mengambil data profil.'
            ]);
        } catch (\Exception $e) {
            // 9. Handle Error Koneksi
            return back()->withErrors([
                '_global' => 'Tidak dapat terhubung ke server backend.'
            ]);
        }
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
