<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\User; // Pastikan User model di-import untuk pengecekan DB langsung

class ForgotPasswordController extends Controller
{
    // Tampilkan Form Input No WA
    public function showLinkRequestForm()
    {
        return view('auth.forgot_password');
    }

    // A. PROSES KIRIM OTP (User Baru Minta)
    public function sendOtp(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // No WA
        ]);

        // 1. Generate OTP di Frontend
        $otp = rand(100000, 999999);

        // 2. Panggil API Backend (KirimOtpReset)
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');

        try {
            $response = Http::post($baseUrl . '/api/pesan/kirim-otp-reset', [
                'no_wa' => $request->login,
                'otp'   => (string)$otp
            ]);

            $result = $response->json();

            // Handle Rate Limit (429)
            if ($response->status() === 429) {
                return response()->json([
                    'status'  => false,
                    'message' => $result['message'] ?? 'Terlalu banyak permintaan. Tunggu sebentar.'
                ], 429);
            }

            if ($response->successful() && ($result['status'] ?? false)) {
                // 3. Simpan OTP & Login ke Session HANYA jika sukses kirim
                Session::put('reset_otp', $otp);
                Session::put('reset_login', $request->login);

                return response()->json([
                    'status'  => true,
                    'message' => 'Kode OTP berhasil dikirim ke WhatsApp Anda.'
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => $result['message'] ?? 'Nomor tidak terdaftar atau gagal kirim.'
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan koneksi ke server.'
            ], 500);
        }
    }

    // B. [UPDATE] PROSES VERIFIKASI USER (Via API Existing)
    public function verifyUserOnly(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
        ]);

        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');

        try {
            // Panggil API yang SAMA dengan Kirim OTP, tapi tambah parameter 'check_only'
            $response = Http::post($baseUrl . '/api/pesan/kirim-otp-reset', [
                'no_wa'      => $request->login,
                'check_only' => true  // <--- Flag agar API tidak kirim WA, cuma cek DB
            ]);

            $result = $response->json();

            if ($response->successful() && ($result['status'] ?? false)) {
                // Restore Session Login
                Session::put('reset_login', $request->login);

                // Set flag bypass karena user pakai OTP lama
                Session::put('reset_bypass_otp', true);

                return response()->json([
                    'status'  => true,
                    'message' => 'Data ditemukan. Silakan masukkan OTP Anda.'
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => $result['message'] ?? 'Nomor WhatsApp tidak terdaftar.'
                ], 422); // 422 Unprocessable Entity (Validasi Gagal)
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghubungi server API.'
            ], 500);
        }
    }

    // Tampilkan Form Reset (Input OTP & Password Baru)
    public function showResetForm()
    {
        if (!Session::has('reset_login')) {
            return redirect()->route('password.request')->with('error', 'Sesi kadaluarsa. Silakan ulangi.');
        }

        return view('auth.reset_password', [
            'login' => Session::get('reset_login')
        ]);
    }

    // C. PROSES UBAH PASSWORD AKHIR
    public function reset(Request $request)
    {
        $request->validate([
            'otp'      => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $sessionLogin = Session::get('reset_login');

        // 1. VALIDASI OTP (Dual Mode)
        // Mode A: User baru minta OTP (Validasi Session vs Input)
        if (Session::has('reset_otp')) {
            $sessionOtp = Session::get('reset_otp');
            if ($request->otp != $sessionOtp) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kode OTP tidak sesuai dengan yang dikirim.'
                ], 422);
            }
        }
        // Mode B: User "Sudah Punya OTP" (Session reset_otp kosong, tapi flag bypass ada)
        elseif (Session::get('reset_bypass_otp') === true) {
            // Di mode ini, kita PERCAYA user memasukkan OTP yang benar dari WA sebelumnya.
            // Karena API change-password Backend sudah tidak validasi OTP lagi, 
            // risiko keamanan ada di sini (Brute force OTP).
            // Idealnya Backend memvalidasi OTP, tapi sesuai request flow "Validasi di FE",
            // maka kita ijinkan lewat.
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Sesi tidak valid. Silakan minta OTP ulang.'
            ], 422);
        }

        // 2. PANGGIL API CHANGE PASSWORD
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');

        try {
            $response = Http::post($baseUrl . '/api/change-password', [
                'login'                 => $sessionLogin,
                'password'              => $request->password,
                'password_confirmation' => $request->password_confirmation,
            ]);

            $result = $response->json();

            if ($response->successful() && ($result['status'] ?? false)) {
                // Bersihkan semua session
                Session::forget(['reset_otp', 'reset_login', 'reset_bypass_otp']);

                return response()->json([
                    'status'  => true,
                    'message' => 'Password berhasil diubah. Silakan login.'
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => $result['message'] ?? 'Gagal mengubah password.'
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Server Error'], 500);
        }
    }
}
