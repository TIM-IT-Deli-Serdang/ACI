<?php

namespace App\Http\Controllers\Backend\LogActivity;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class LogActivityController extends Controller
{
    // 👉 Halaman Log Activity
    public function index() 
    {
        return view('backend.help.log_activity.index');
    }

    // 👉 Ambil data dari backend API untuk DataTables
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
                ->get($baseUrl . '/api/log-activity', $request->all());
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
                'message' => $response->json()['message'] ?? 'Gagal mengambil data log activity.'
            ], $response->status());
        }

        // RETURN DATA KE DATATABLES
        return response()->json($response->json());
    }


   public function show($id, Request $request)
{
    $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
    $token   = Session::get('auth_token');

    if (!$token) {
        return redirect()->route('login')->withErrors([
            '_global' => 'Sesi anda telah habis, silahkan login kembali'
        ]);
    }

    // Forward User-Agent dan IP dari browser ke backend API
    $userAgent = $request->header('User-Agent', 'unknown');
    $clientIp  = $request->header('X-Real-IP') 
                ?? $request->header('X-Forwarded-For') 
                ?? $request->ip();

    try {
        // Ambil detail dari backend
        $response = Http::withHeaders([
                'User-Agent' => $userAgent,
                'X-Forwarded-For' => $clientIp,
                'X-Real-IP' => $clientIp,
            ])
            ->withToken($token)
            ->get($baseUrl . '/api/log-activity/' . $id);
    } catch (\Exception $e) {
        return back()->withErrors([
            '_global' => 'Tidak dapat menghubungi server backend.'
        ]);
    }

    if (!$response->ok()) {
        return back()->withErrors([
            '_global' => $response->json('message') ?? 'Gagal mengambil data activity.'
        ]);
    }

    $data = $response->json();

    return view('backend.help.log_activity.show', compact('data'));
}


}
