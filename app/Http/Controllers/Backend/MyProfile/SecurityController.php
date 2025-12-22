<?php

namespace App\Http\Controllers\Backend\MyProfile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SecurityController extends Controller
{
    public function index(Request $request)
    {
        $akun = Auth::user();
        return view('backend.my_profile.security.index', compact('akun'));
    }

   public function edit($id): JsonResponse
{
    $userData = Session::get('user');

    // Jika session kosong atau id tidak cocok, tolak
    if (!$userData || (string)($userData['id'] ?? $userData->id ?? '') !== (string)$id) {
        return response()->json([
            'error' => 'Data user tidak ditemukan atau sesi tidak sesuai.'
        ], 404);
    }

    $user = is_object($userData) ? $userData : (object) $userData;

    $html = view('backend.my_profile.security.edit', compact('user'))->render();

    return response()->json(['html' => $html]);
}

    /**
     * Update password & email via backend API (/api/security)
     *
     * @param Request $request
     * @param int|string|null $id  (tidak dipakai, hanya untuk kompatibilitas)
     * @return JsonResponse
     */
    public function update(Request $request, $id = null): JsonResponse
{
    $formattedTime = Carbon::now()->diffForHumans();
    $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
    $token   = Session::get('auth_token');

    if (!$token) {
        return response()->json([
            'error' => 'Anda belum login atau sesi telah berakhir.'
        ], 401);
    }

    $payload = $request->only([
        'email',
        'current_password',
        'new_password',
        'new_confirm_password'
    ]);

    try {
        $response = Http::withToken($token)
            ->acceptJson()
            ->put($baseUrl . '/api/security', $payload);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Tidak dapat menghubungi server backend.'
        ], 500);
    }

    // 401 → logout
    if ($response->status() === 401) {
        Session::forget('auth_token');
        Session::forget('user');

        return response()->json([
            'error' => 'Sesi berakhir. Silakan login ulang.',
            'force_logout' => true
        ], 401);
    }

    // 422 → validation
    if ($response->status() === 422) {
        return response()->json([
            'errors' => $response->json('errors') ?? []
        ], 422);
    }

    // error lain
    if (!$response->ok()) {
        return response()->json([
            'error' => $response->json('error') ?? 'Terjadi kesalahan.'
        ], $response->status());
    }

    // ===== SUCCESS =====
    $resp = $response->json();

    // 🔐 PASSWORD BERUBAH → PAKSA LOGOUT
    if (!empty($resp['force_logout'])) {
        Session::forget('auth_token');
        Session::forget('user');

        return response()->json([
            'success'      => $resp['success'] ?? 'Password berhasil diubah.',
            'force_logout' => true
        ], 200);
    }

    // SUCCESS NORMAL (kalau suatu hari endpoint dipakai bukan password)
    return response()->json([
        'success' => $resp['success'] ?? 'Berhasil diperbarui.',
        'time'    => $formattedTime,
        'judul'   => 'Berhasil'
    ], 200);
}

}
