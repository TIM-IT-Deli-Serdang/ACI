<?php
    
    namespace App\Http\Controllers\Backend\MyProfile;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;
use Session;
use Illuminate\Support\Facades\Http;
    
class LoginSessionController extends Controller
{
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('backend.my_profile.login_session.index');
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
                ->get($baseUrl . '/api/login-session', $request->all());
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
                'message' => $response->json()['message'] ?? 'Gagal mengambil data my activity.'
            ], $response->status());
        }

        // RETURN DATA KE DATATABLES
        return response()->json($response->json());
    }
   
    
  
    
}