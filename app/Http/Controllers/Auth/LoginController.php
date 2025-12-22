<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    protected function baseUrl(): string
    {
        return rtrim(env('API_BASE_URL', ''), '/');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $baseUrl = $this->baseUrl();

        if (empty($baseUrl)) {
            $err = ['_global' => 'API_BASE_URL belum diset di .env'];
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => $err['_global']], 500);
            }
            return back()->withErrors($err)->withInput();
        }

        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->messages();
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => 'Validation error', 'errors' => $errors], 422);
            }
            return back()->withErrors($errors)->withInput();
        }

        $payload = $request->only(['login', 'password']);
        $userAgent = $request->header('User-Agent', 'unknown');
        $clientIp  = $request->header('X-Real-IP') ?? $request->header('X-Forwarded-For') ?? $request->ip();

        try {
            $resp = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => $userAgent,
                    'X-Forwarded-For' => $clientIp,
                    'X-Real-IP' => $clientIp,
                ])
                ->post($baseUrl . '/api/login', $payload);
        } catch (\Throwable $e) {
            Log::error('API login error: '.$e->getMessage(), ['exception' => $e]);
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat menghubungi server backend.'], 500);
            }
            return back()->withErrors(['_global' => 'Tidak dapat menghubungi server backend.'])->withInput();
        }

        $status = $resp->status();
        $body = $resp->json() ?? [];

        if ($status === 422) {
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => $body['message'] ?? 'Validation failed', 'errors' => $body['errors'] ?? []], 422);
            }
            return back()->withErrors($body['errors'] ?? $body)->withInput();
        }

        if ($status === 429) {
            $msg = $body['message'] ?? 'Too many attempts';
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => $msg], 429);
            }
            return back()->withErrors(['_global' => $msg])->withInput();
        }

        if (!$resp->successful()) {
            $msg = $body['message'] ?? $body['error'] ?? 'Login gagal';
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => $msg], $status);
            }
            return back()->withErrors(['_global' => $msg])->withInput();
        }

        $token = $body['token'] ?? $body['access_token'] ?? null;

        if (empty($token)) {
            $msg = $body['message'] ?? 'Login gagal. Token tidak diterima.';
            Log::warning('Login response missing token', ['response' => $body]);
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => $msg], 500);
            }
            return back()->withErrors(['_global' => $msg])->withInput();
        }

        // --- PANGGIL /api/me SEKALI untuk dapat roles & permissions ---
        $userObj = $body['user'] ?? $body['data'] ?? $body;
        try {
            $meResp = Http::timeout(10)
                ->withToken($token)
                ->withHeaders([
                    'User-Agent' => $userAgent,
                    'X-Forwarded-For' => $clientIp,
                    'X-Real-IP' => $clientIp,
                    'Accept' => 'application/json',
                ])
                ->get($baseUrl . '/api/me');

            if ($meResp->successful()) {
                $meBody = $meResp->json() ?? [];
                // backend mungkin mengembalikan { status: true, data: {...} } atau { status:true, user: {...} }
                $userObj = $meBody['data'] ?? $meBody['user'] ?? $meBody;
            } else {
                Log::warning('Failed to fetch /api/me after login', ['status' => $meResp->status(), 'body' => $meResp->json()]);
            }
        } catch (\Throwable $e) {
            Log::error('Error fetching /api/me after login: '.$e->getMessage(), ['exception' => $e]);
        }

        // --- Normalisasi roles & permissions ke array string ---
        $roles = $userObj['roles'] ?? [];
        if (! is_array($roles)) {
            $roles = array_map(function($r){
                if (is_array($r) && isset($r['name'])) return $r['name'];
                if (is_object($r) && isset($r->name)) return $r->name;
                return $r;
            }, (array)$roles);
        }
        $roles = array_values(array_filter($roles));

        $permissions = $userObj['permissions'] ?? $userObj['perms'] ?? [];
        if (! is_array($permissions)) {
            $permissions = array_map(function($p){
                if (is_array($p) && isset($p['name'])) return $p['name'];
                if (is_object($p) && isset($p->name)) return $p->name;
                return $p;
            }, (array)$permissions);
        }
        $permissions = array_values(array_filter($permissions));

        // Log jika roles/permissions kosong supaya mudah debugging
        if (empty($roles) || empty($permissions)) {
            Log::info('login: roles/permissions empty for user after /me fetch', [
                'userId' => $userObj['id'] ?? null,
                'roles' => $roles,
                'permissions' => $permissions,
            ]);
        }

        // Normalized user to store in session
        $normalized = [
            'id' => $userObj['id'] ?? null,
            'name' => $userObj['name'] ?? $userObj['full_name'] ?? null,
            'email' => $userObj['email'] ?? null,
            'no_wa' => $userObj['no_wa'] ?? null,
            'roles' => $roles,
            'permissions' => $permissions,
            'avatar_url' => $userObj['avatar_url'] ?? $userObj['avatar'] ?? null,
        ];

        Session::put('auth_token', $token);
        Session::put('user', $normalized);

        if ($request->expectsJson()) {
            return response()->json(['status' => true, 'message' => 'Login sukses', 'token' => $token, 'user' => $normalized], 200);
        }

        return redirect()->intended(route('dashboard.index'));
    }

    public function logout(Request $request)
    {
        $baseUrl = $this->baseUrl();
        $token = Session::get('auth_token');

        $userAgent = $request->header('User-Agent', 'unknown');
        $clientIp  = $request->header('X-Real-IP') ?? $request->header('X-Forwarded-For') ?? $request->ip();

        if ($token && !empty($baseUrl)) {
            try {
                $resp = Http::timeout(10)
                    ->withToken($token)
                    ->withHeaders([
                        'User-Agent' => $userAgent,
                        'X-Forwarded-For' => $clientIp,
                        'X-Real-IP' => $clientIp,
                        'Accept' => 'application/json',
                    ])
                    ->post($baseUrl . '/api/logout');

                if (! $resp->successful()) {
                    Log::warning('API logout non-success', ['status' => $resp->status(), 'body' => $resp->json()]);
                }
            } catch (\Throwable $e) {
                Log::error('Error calling API logout: '.$e->getMessage(), ['exception' => $e]);
            }
        }

        Session::forget('auth_token');
        Session::forget('user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
