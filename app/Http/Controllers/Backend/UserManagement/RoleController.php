<?php
    
namespace App\Http\Controllers\Backend\UserManagement;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('backend.user_management.role.index');
    }


    public function getPermissions(Request $request)
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
        // optional: forward query params (mis. cache_minutes)
        $response = Http::withToken($token)
            ->get($baseUrl . '/api/permissions', $request->all());
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

    if (!$response->ok()) {
        return response()->json([
            'status' => false,
            'message' => $response->json()['message'] ?? 'Gagal mengambil data permission.'
        ], $response->status());
    }

    // sukses -> return json yang sama seperti API backend
    return response()->json($response->json(), 200);
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
                ->get($baseUrl . '/api/roles/datatables', $request->all());
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
                'message' => $response->json()['message'] ?? 'Gagal mengambil data Role.'
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
                ->post($baseUrl . '/api/roles', $request->all());
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
                'message' => $response->json()['message'] ?? 'Gagal menyimpan data User.'
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
                ->get($baseUrl . '/api/roles/' . $id);
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
                'message' => $response->json()['message'] ?? 'Gagal mengambil data User.'
            ], $response->status());
        }

        // misal API mengembalikan detail di root atau di 'data'
        $data = $response->json();
        if (isset($data['data'])) {
            $data = $data['data'];
        }

        // render view partial (blade) dan kirim sebagai html
        $html = view('backend.user_management.role.show', ['data' => $data])->render();

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
        // 1) ambil role detail dari backend API
        $respRole = Http::withToken($token)->get($baseUrl . '/api/roles/' . $id);
    } catch (\Exception $e) {
        return response()->json(['status' => false, 'message' => 'Tidak dapat menghubungi server backend (role).'], 500);
    }

    if ($respRole->status() === 401) {
        return response()->json(['status' => false, 'message' => 'Token tidak valid atau sudah expired.'], 401);
    }
    if ($respRole->status() === 404) {
        return response()->json(['status' => false, 'message' => 'Data role tidak ditemukan.'], 404);
    }
    if (!$respRole->ok()) {
        return response()->json(['status' => false, 'message' => $respRole->json()['message'] ?? 'Gagal mengambil data role.'], $respRole->status());
    }

    $roleData = $respRole->json();
    if (isset($roleData['data'])) $roleData = $roleData['data'];

    // normalisasi: pastikan $roleData berisi 'permissions' sbg array (bisa object atau id)
    $rolePermissionIds = [];
    if (!empty($roleData['permissions']) && is_array($roleData['permissions'])) {
        foreach ($roleData['permissions'] as $p) {
            // toleransi: permission bisa berupa id atau object {id:..}
            if (is_array($p) && isset($p['id'])) $rolePermissionIds[] = $p['id'];
            else if (is_numeric($p)) $rolePermissionIds[] = (int) $p;
        }
    } elseif (!empty($roleData['permission_ids']) && is_array($roleData['permission_ids'])) {
        // alternatif nama field
        $rolePermissionIds = array_map('intval', $roleData['permission_ids']);
    }

    // 2) ambil seluruh permissions dari API (grouped atau flat)
    try {
        $respPerm = Http::withToken($token)->get($baseUrl . '/api/permissions'); // sesuaikan endpoint
    } catch (\Exception $e) {
        return response()->json(['status' => false, 'message' => 'Tidak dapat menghubungi server backend (permissions).'], 500);
    }

    if (!$respPerm->ok()) {
        return response()->json(['status' => false, 'message' => $respPerm->json()['message'] ?? 'Gagal mengambil permissions.'], $respPerm->status());
    }

    $permData = $respPerm->json();
    if (isset($permData['data'])) {
        $permList = $permData['data']; // bisa grouped (associative) atau flat (array)
    } else {
        $permList = $permData;
    }

    // jika API mengembalikan flat array of perms, kita groupBy('category') di frontend (mirip server-side sebelumnya)
    // Pastikan struktur permission: each item punya 'id', 'name', dan opsional 'category'
    $grouped = [];
    if (is_array($permList) && count($permList) > 0) {
        // if permList is grouped (assoc: category => [..])
        $firstKey = array_keys($permList)[0];
        if (is_string($firstKey) && is_array($permList[$firstKey]) && isset($permList[$firstKey][0]['id'])) {
            // already grouped
            $grouped = $permList;
        } else {
            // flat array -> group by 'category' if exists, else by prefix before dot or 'Other'
            foreach ($permList as $p) {
                $cat = $p['category'] ?? null;
                if (!$cat && isset($p['name']) && strpos($p['name'], '.') !== false) {
                    $cat = explode('.', $p['name'])[0];
                }
                if (!$cat) $cat = 'Other';
                if (!isset($grouped[$cat])) $grouped[$cat] = [];
                $grouped[$cat][] = $p;
            }
        }
    }

    // 3) render view partial dengan $role, $permission (grouped), $rolePermissions (array ids)
    $html = view('backend.user_management.role.edit', [
        'role' => $roleData, // berisi id, name, etc
        'permission' => $grouped,
        'rolePermissions' => array_values($rolePermissionIds), // array of ids
    ])->render();

    return response()->json(['html' => $html]);
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
                ->put($baseUrl . '/api/roles/' . $id, $request->all());
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

       try {
    $token = Session::get('auth_token');
    $base  = rtrim(env('API_BASE_URL',''), '/');

    if ($token && !empty($base)) {
        $meResp = Http::withToken($token)
            ->withHeaders(['Accept' => 'application/json'])
            ->get($base . '/api/me');

        if ($meResp->successful()) {
            $meBody = $meResp->json() ?? [];
            $userObj = $meBody['data'] ?? $meBody['user'] ?? $meBody;

            // normalisasi seperti di LoginController
            $roles = $userObj['roles'] ?? [];
            if (!is_array($roles)) {
                $roles = array_map(function ($r) {
                    if (is_array($r) && isset($r['name'])) return $r['name'];
                    if (is_object($r) && isset($r->name)) return $r->name;
                    return $r;
                }, (array)$roles);
            }
            $roles = array_values(array_filter($roles));

            $permissions = $userObj['permissions'] ?? $userObj['perms'] ?? [];
            if (!is_array($permissions)) {
                $permissions = array_map(function ($p) {
                    if (is_array($p) && isset($p['name'])) return $p['name'];
                    if (is_object($p) && isset($p->name)) return $p->name;
                    return $p;
                }, (array)$permissions);
            }
            $permissions = array_values(array_filter($permissions));

            $normalized = [
                'id' => $userObj['id'] ?? null,
                'name' => $userObj['name'] ?? $userObj['full_name'] ?? null,
                'email' => $userObj['email'] ?? null,
                'roles' => $roles,
                'no_wa' => $userObj['no_wa'] ?? null,
                'permissions' => $permissions,
                'avatar_url' => $userObj['avatar_url'] ?? $userObj['avatar'] ?? null,
            ];

            Session::put('user', $normalized);
        } else {
            \Log::warning('Refresh /api/me after update returned non-success', ['status' => $meResp->status(), 'body' => $meResp->json()]);
        }
    }
} catch (\Throwable $e) {
    \Log::error('Error refreshing /api/me after update: ' . $e->getMessage());
}

// kembalikan response API asli (atau custom)
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
            $response = Http::withToken($token)->delete($baseUrl . '/api/roles/' . $id);
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
    



      public function select(Request $request)
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
        // optional: forward query params (mis. cache_minutes)
        $response = Http::withToken($token)
            ->get($baseUrl . '/api/roles/select', $request->all());
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

    if (!$response->ok()) {
        return response()->json([
            'status' => false,
            'message' => $response->json()['message'] ?? 'Gagal mengambil data permission.'
        ], $response->status());
    }

    // sukses -> return json yang sama seperti API backend
    return response()->json($response->json(), 200);
}



    
    
}