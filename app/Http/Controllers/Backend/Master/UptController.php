<?php

namespace App\Http\Controllers\Backend\Master;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class UptController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('backend.master.upt.index');
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
                ->get($baseUrl . '/api/upt/datatables', $request->all());
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

        try {
            // Forward semua input ke API. Jika butuh hanya beberapa field, filter di sini.
            $response = Http::withToken($token)
                ->post($baseUrl . '/api/upt', $request->all());
        } catch (\Throwable $e) {
            // Gagal koneksi ke backend
            return response()->json([
                'status' => false,
                'message' => 'Tidak dapat menghubungi server backend.'
            ], 500);
        }

        // Jika API mengembalikan Unauthorized
        if ($response->status() === 401) {
            return response()->json([
                'status' => false,
                'message' => 'Token tidak valid atau sudah expired.'
            ], 401);
        }

        // Jika API mengembalikan validation error
        if ($response->status() === 422) {
            // pastikan format errors diteruskan persis seperti API
            return response()->json([
                'status'  => 422,
                'message' => $response->json()['message'] ?? 'Validation Error',
                'errors'  => $response->json()['errors'] ?? []
            ], 422);
        }

        // Jika ada error lain dari API
        if (!$response->ok()) {
            return response()->json([
                'status' => false,
                'message' => $response->json()['message'] ?? 'Gagal menyimpan data Laporan.'
            ], $response->status());
        }

        // Berhasil (mis. 201 Created) -> teruskan body API ke frontend beserta status code
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
                ->get($baseUrl . '/api/upt/' . $id);
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
        $html = view('backend.master.upt.show', ['data' => $data])->render();

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
            $response = Http::withToken($token)->get($baseUrl . '/api/upt/' . $id);
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

        $html = view('backend.master.upt.edit', ['data' => $data])->render();
        return response()->json(['html' => $html], 200);
    }


    public function update(Request $request, $id)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) {
            return response()->json(['status' => false, 'message' => 'Anda belum login.'], 401);
        }

        try {
            // Jika butuh file upload, gunakan multipart/form-data. Berikut asumsinya form-data biasa.
            $response = Http::withToken($token)
                ->put($baseUrl . '/api/upt/' . $id, $request->all());
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => 'Tidak dapat menghubungi server backend.'], 500);
        }

        if ($response->status() === 401) {
            return response()->json(['status' => false, 'message' => 'Token tidak valid atau sudah expired.'], 401);
        }

        if ($response->status() === 422) {
            return response()->json([
                'status'  => 422,
                'message' => $response->json()['message'] ?? 'Validation Error',
                'errors'  => $response->json()['errors'] ?? []
            ], 422);
        }

        if (!$response->ok()) {
            return response()->json(['status' => false, 'message' => $response->json()['message'] ?? 'Gagal memperbarui data.'], $response->status());
        }

        // Berhasil -> teruskan body API (mis. message) dan status
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
            $response = Http::withToken($token)->delete($baseUrl . '/api/upt/' . $id);
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
}
