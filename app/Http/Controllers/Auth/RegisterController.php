<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    protected function baseUrl(): string
    {
        // Pastikan API_BASE_URL ada di .env
        return rtrim(env('API_BASE_URL', ''), '/');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $baseUrl = $this->baseUrl();

        if (empty($baseUrl)) {
            return response()->json(['status' => false, 'message' => 'Konfigurasi Server Error (API URL missing).'], 500);
        }

        // 1. Validasi Input di Sisi Frontend (Opsional tapi disarankan)
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'nullable|email',
            'no_wa'    => 'required|string|min:10|max:15',
            'nik'      => 'required|string|min:16|max:20',
            'password' => 'required|string|min:6|confirmed', // Pastikan ada field password_confirmation di view
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi Gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // 2. Kirim Data ke API Backend (/api/pengguna)
        try {
            // Siapkan payload sesuai permintaan API Backend
            $payload = [
                'name'     => $request->name,
                'email'    => $request->email,
                'no_wa'    => $request->no_wa,
                'nik'      => $request->nik,
                'password' => $request->password,
            ];

            // Karena endpoint API dilindungi middleware auth:sanctum atau public? 
            // Jika public, langsung post. Jika perlu token khusus client, tambahkan header.
            // Asumsi: Endpoint register masyarakat bersifat public atau menggunakan client credentials.
            // Namun melihat kode API Controller Anda: $this->middleware('auth:sanctum') di __construct.
            // JIKA API BUTUH AUTH, Anda harus punya "Super Token" atau endpoint harus dibuka (except).
            // UNTUK REGISTRASI UMUM, BIASANYA ENDPOINT DIBUAT PUBLIC di Backend.
            // Jika backend mewajibkan auth, kode ini akan return 401.
            
            $response = Http::timeout(15)
                ->withHeaders(['Accept' => 'application/json'])
                ->post($baseUrl . '/api/pengguna', $payload);

            $body = $response->json();

            if ($response->successful()) {
                return response()->json([
                    'status'  => true,
                    'message' => 'Registrasi Berhasil! Silakan login.',
                    'redirect' => route('login')
                ], 200);
            } else {
                // Handle error dari API (misal validasi backend)
                return response()->json([
                    'status'  => false,
                    'message' => $body['message'] ?? 'Gagal Mendaftar',
                    'errors'  => $body['errors'] ?? []
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Register Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan koneksi ke server.'], 500);
        }
    }
}