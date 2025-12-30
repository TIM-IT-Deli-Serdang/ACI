<?php

namespace App\Http\Controllers\Backend\LogActivity;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class LogActivityController extends Controller
{
    public function index()
    {
        return view('backend.help.log_activity.index');
    }

    /**
     * Ambil info user login dari session (dibuat fleksibel).
     * - $userId bisa null (kalau session tidak menyimpan id)
     * - $userName dipakai untuk filter kalau id tidak ada / data log pakai nama
     * - $role untuk deteksi superadmin
     */
    private function getCurrentUser(): array
    {
        $user = Session::get('user')
            ?? Session::get('auth_user')
            ?? Session::get('userdata')
            ?? [];

        $userId = $user['id'] ?? $user['user_id'] ?? Session::get('user_id') ?? Session::get('auth_id') ?? null;

        $userName = $user['name']
            ?? $user['username']
            ?? $user['nama']
            ?? Session::get('user_name')
            ?? Session::get('auth_name')
            ?? null;

        // role bisa beda struktur
        $role = $user['role']
            ?? $user['role_name']
            ?? ($user['role']['name'] ?? null)
            ?? Session::get('role')
            ?? null;

        $role = is_string($role) ? strtolower($role) : null;

        return [$userId, $userName, $role];
    }

    private function isSuperadmin(?string $role): bool
    {
        return in_array($role, ['superadmin', 'super admin'], true);
    }

    /**
     * Ambil "causer" dari 1 row log (fleksibel).
     */
    private function extractCauser($row): array
    {
        // bisa array atau object
        $r = is_array($row) ? $row : (array)$row;

        $causerId = $r['causer_id'] ?? $r['causerId'] ?? ($r['causer']['id'] ?? null);
        $causerName =
            $r['causer_name']
            ?? $r['causerName']
            ?? ($r['causer']['name'] ?? null)
            ?? ($r['causer']['username'] ?? null)
            ?? ($r['causer_id'] ?? null); // di UI kamu "causer_id" tampilnya nama

        return [$causerId, $causerName];
    }

    public function getData(Request $request)
    {
        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        $token   = Session::get('auth_token');

        if (!$token) {
            return response()->json([
                'draw' => (int)($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Anda belum login.'
            ], 401);
        }

        [$userId, $userName, $role] = $this->getCurrentUser();
        $isSuper = $this->isSuperadmin($role);

        // simpan paging asli DataTables
        $draw  = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        // kirim request ke backend
        // kalau non-superadmin: kita ambil banyak dulu supaya bisa filter + paging sendiri
        $params = $request->all();
        if (!$isSuper) {
            $params['start'] = 0;
            $params['length'] = 10000; // ambil banyak, lalu kita slice sesuai paging datatables
        }

        try {
            $response = Http::withToken($token)
                ->get($baseUrl . '/api/log-activity', $params);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Tidak dapat menghubungi server backend.'
            ], 500);
        }

        if ($response->status() === 401) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Token tidak valid atau sudah expired.'
            ], 401);
        }

        if ($response->status() === 403) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'HTTP 403 - cek permission role (log.activity.list)'
            ], 403);
        }

        if (!$response->ok()) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $response->json('message') ?? 'Gagal mengambil data log activity.'
            ], $response->status());
        }

        $json = $response->json();

        // DataTables biasanya: { draw, recordsTotal, recordsFiltered, data: [] }
        $rows = $json['data'] ?? [];

        // ✅ Jika superadmin → langsung balikin apa adanya
        if ($isSuper) {
            return response()->json($json);
        }

        // ✅ Jika UPT/SDA → filter hanya milik dirinya
        $needleName = $userName ? strtolower(trim($userName)) : null;

        $filtered = array_values(array_filter($rows, function ($row) use ($userId, $needleName) {
            [$cid, $cname] = $this->extractCauser($row);

            if ($userId !== null && $cid !== null && (string)$cid === (string)$userId) {
                return true;
            }

            if ($needleName && $cname) {
                return strtolower(trim((string)$cname)) === $needleName;
            }

            return false;
        }));

        // ✅ paging sendiri sesuai request DataTables
        $totalFiltered = count($filtered);
        $pageData = array_slice($filtered, $start, $length);

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalFiltered,
            'recordsFiltered' => $totalFiltered,
            'data' => $pageData,
        ]);
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

        [$userId, $userName, $role] = $this->getCurrentUser();
        $isSuper = $this->isSuperadmin($role);

        try {
            $response = Http::withToken($token)
                ->get($baseUrl . '/api/log-activity/' . $id);
        } catch (\Exception $e) {
            return back()->withErrors(['_global' => 'Tidak dapat menghubungi server backend.']);
        }

        if ($response->status() === 403) {
            return back()->withErrors(['_global' => 'HTTP 403 - cek permission role (log.activity.show)']);
        }

        if (!$response->ok()) {
            return back()->withErrors(['_global' => $response->json('message') ?? 'Gagal mengambil data activity.']);
        }

        $data = $response->json();
        $row = $data['data'] ?? $data;

        // ✅ proteksi: non-superadmin tidak boleh lihat log user lain
        if (!$isSuper) {
            [$cid, $cname] = $this->extractCauser($row);

            $okById = ($userId !== null && $cid !== null && (string)$cid === (string)$userId);
            $okByName = ($userName && $cname && strtolower(trim((string)$cname)) === strtolower(trim((string)$userName)));

            if (!$okById && !$okByName) {
                abort(403, 'Anda tidak boleh melihat log milik user lain.');
            }
        }

        return view('backend.help.log_activity.show', compact('data'));
    }
}
