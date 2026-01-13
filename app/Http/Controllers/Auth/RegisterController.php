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

    // Di file: app/Http/Controllers/Auth/RegisterController.php

    public function kirimOtp(Request $request)
    {
        // 1. Validasi Input No WA
        $validator = Validator::make($request->all(), [
            'no_wa' => 'required|string|min:10|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Nomor WhatsApp tidak valid.'], 422);
        }

        // 2. Generate OTP (6 Digit)
        $otp = rand(100000, 999999);
        $noWa = $request->no_wa;

        // 3. Simpan ke Session Frontend (Agar bisa divalidasi nanti saat submit register)
        session([
            'register_otp' => (string) $otp,
            'register_wa'  => $noWa,
            'otp_expires'  => now()->addMinutes(5)
        ]);

        // 4. KIRIM REQUEST KE BACKEND API (Bukan ke WA Gateway langsung)
        try {
            // Ambil Base URL API dari fungsi yang sudah ada
            $baseUrl = $this->baseUrl();

            // Tembak Endpoint Backend Anda: /api/pesan/kirim-otp
            // Payload disesuaikan dengan permintaan KirimPesanController (no_wa, otp)
            $response = Http::timeout(10)->post($baseUrl . '/api/pesan/kirim-otp', [
                'no_wa' => $noWa,
                'otp'   => (string) $otp,
            ]);

            // Cek Response dari Backend
            if ($response->successful() && $response->json()['status'] === true) {
                return response()->json([
                    'status' => true,
                    'message' => 'OTP berhasil dikirim ke WhatsApp.'
                ]);
            } else {
                // Jika Backend gagal mengirim (misal nomor salah atau server WA down)
                $msg = $response->json()['message'] ?? 'Gagal mengirim OTP dari server.';
                return response()->json(['status' => false, 'message' => $msg], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Koneksi ke Backend Gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    // Tambahkan di dalam class RegisterController

    public function verifikasiOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string',
            'no_wa' => 'required|string'
        ]);

        // 1. Ambil data dari Session
        $sessionOtp = session('register_otp');
        $sessionWa  = session('register_wa');
        $expires    = session('otp_expires');

        // 2. Cek apakah OTP ada di session
        if (!$sessionOtp || !$expires) {
            return response()->json(['status' => false, 'message' => 'OTP kadaluarsa atau belum diminta.'], 400);
        }

        // 3. Cek Kadaluarsa Waktu
        if (now()->greaterThan($expires)) {
            return response()->json(['status' => false, 'message' => 'Waktu OTP habis. Silakan kirim ulang.'], 400);
        }

        // 4. Cek Kesesuaian Nomor WA (Agar tidak pakai OTP orang lain)
        if ($request->no_wa != $sessionWa) {
            return response()->json(['status' => false, 'message' => 'Nomor WhatsApp berubah. Kirim ulang OTP.'], 400);
        }

        // 5. Cek Kode OTP
        if ($request->otp === $sessionOtp) {
            // Jika Benar
            return response()->json([
                'status' => true,
                'message' => 'OTP Valid.'
            ]);
        } else {
            // Jika Salah
            return response()->json(['status' => false, 'message' => 'Kode OTP Salah.'], 400);
        }
    }
}
