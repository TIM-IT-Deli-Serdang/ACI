<?php

namespace App\Http\Controllers\API\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MasterUpt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\PermissionRegistrar;

/**
 * @OA\Tag(
 *     name="User Management",
 *     description="API untuk manajemen data user"
 * )
 */



class UserController extends Controller
{


   public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:user.list',   ['only' => ['index','getData']]);
        $this->middleware('permission:user.create', ['only' => ['store']]);
        $this->middleware('permission:user.show',   ['only' => ['show']]);
        $this->middleware('permission:user.update', ['only' => ['update']]);
        $this->middleware('permission:user.delete', ['only' => ['destroy']]);
    }





    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="List User dengan global search dan pagination",
     *     tags={"User"},
     *     security={{"Bearer":{}}},
     *
     *     @OA\Parameter(
	 * 		name="search", 
	 * 		in="query", 
	 * 		required=false, 
	 * 		description="Global search: name, email, no_wa",
	 * 		@OA\Schema(type="string")),
	 * 
	 * 
     *     @OA\Parameter(
	 * 		name="page", in="query", 
	 * 		required=false, 
	 * 		description="Nomor halaman",
	 * 		@OA\Schema(type="integer", example=1)),
	 * 
	 * 
     *     @OA\Parameter(
	 * 		name="per_page", 
	 * 		in="query", 
	 * 		required=false, 
	 * 		description="Jumlah item per halaman",
	 *		@OA\Schema(type="integer", example=10)),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/User")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50),
     *                 @OA\Property(property="total_pages", type="integer", example=5),
     *                 @OA\Property(property="search", type="string", example="admin")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $search   = $request->search;
        $perPage  = $request->per_page ?? 10;
        $page     = $request->page ?? 1;

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('no_wa', 'ILIKE', "%{$search}%");
            });
        }

        $paginator = $query->orderBy('name', 'ASC')
                           ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status'  => 200,
            'message' => 'Success',
            'data'    => $paginator->items(),
            'meta'    => [
                'page'        => $paginator->currentPage(),
                'per_page'    => $paginator->perPage(),
                'total'       => $paginator->total(),
                'total_pages' => $paginator->lastPage(),
                'search'      => $search
            ]
        ], 200);
    }



/**
     * @OA\Get(
     *     path="/api/users/datatables",
     *     summary="List Users untuk DataTables (server-side)",
     *     tags={"User"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="search[value]",
     *         in="query",
     *         description="Nilai pencarian global (DataTables)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="start",
     *         in="query",
     *         @OA\Schema(type="integer", example=0)
     *     ),
     *     @OA\Parameter(
     *         name="length",
     *         in="query",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="draw", type="integer", example=1),
     *             @OA\Property(property="recordsTotal", type="integer", example=50),
     *             @OA\Property(property="recordsFiltered", type="integer", example=50),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="email", type="string"),
     *                     @OA\Property(property="no_Wa", type="string"),
     *                   
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     */
    public function getData(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $postsQuery = User::select([
            'id',
            'name',
            'email',
            'no_wa',
            'avatar',
            'last_login_at',
            'last_login_ip',
            'created_at'
        ])
        ->with(['roles:id,name']);
          if (!empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $postsQuery->where(function ($query) use ($searchValue) {
                    $query->where('name', 'LIKE', "%{$searchValue}%")
                          ->orWhere('email', 'LIKE', "%{$searchValue}%")
                          ->orWhere('no_wa', 'LIKE', "%{$searchValue}%");
                });
            }
    


         $data = $postsQuery->select('*');
            return \DataTables::of($data) 
                ->addIndexColumn()
           



            ->addColumn('avatar', function($row) {
                    if ($row->avatar) {
                       
                            // Jika avatar ada, tetapi bukan dari Google (misalnya dari aplikasi Laravel)
                            return ' <div class="d-flex align-items-center">
                                        <div class="symbol symbol-45px me-5">
                                           
                                                <img src="' . asset('storage/user/avatar/' . $row->avatar) . '" alt="' . $row->name . '"  />
                                        
                                        </div>
                                        <div class="d-flex flex-column">
                                            <a class="text-gray-800 text-hover-primary mb-1">' . $row->name . '</a>
                                            <span>' . $row->email . '</span>
                                            
                                        </div>
                                    </div>';
                        
                    } else {
                        // Jika avatar kosong, tampilkan huruf pertama dari nama pengguna
                        $initial = strtoupper(substr($row->name, 0, 1));
                        return '<div class="d-flex align-items-center">
                                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                        <div class="symbol-label fs-3 bg-light-primary text-primary">' . $initial . '</div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a class="text-gray-800 text-hover-primary mb-1">' . $row->name . '</a>
                                        <span>' . $row->email . '</span>
                                    </div>
                                </div>';
                    }
                })

                      ->addColumn('roles', function($row) {
            $names = $row->roles->pluck('name')->toArray();
            if (empty($names)) return 'no roles assigned';
            return e(implode(', ', $names));
        })


                ->addColumn('last_login_at', function($row) {
                    if ($row->last_login_at) {
                        $formattedTime = Carbon::parse($row->last_login_at)->diffForHumans();
                        return '<div class="badge badge-light fw-bold">' . $formattedTime . '</div>';
                    } else {
                        return '<div class="badge badge-light fw-bold">Never logged in</div>';
                    }
                })
                ->addColumn('last_login_ip', function($row) {
                    return '<div class="badge badge-light fw-bold">' . ($row->last_login_ip ?: 'N/A') . '</div>';
                })
                ->addColumn('joined_date', function($row) {
                    if ($row->created_at) {
                        $formattedTime = Carbon::parse($row->created_at)
                                            ->locale('id')  // Set locale to Indonesian
                                            ->translatedFormat('d F Y, H:i');
                        return '<div class="badge badge-light fw-bold">' . $formattedTime . '</div>';
                    } else {
                        return '<div class="badge badge-light fw-bold">N/A</div>';
                    }
                })


            

            ->rawColumns(['roles', 'last_login_at','avatar','last_login_ip','joined_date'])

            ->make(true);
    }

   
/**
 * @OA\Post(
 *     path="/api/users",
 *     operationId="storeUser",
 *     summary="Tambah User Baru",
 *     description="Membuat user baru. Field `roles` bisa berupa id (integer), nama (string), atau array berisi id/nama.",
 *     tags={"User"},
 *     security={{"Bearer":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","no_wa","password","roles","nik","upt_id"},
 *
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 example="User Baru",
 *                 description="Nama lengkap user"
 *             ),
 *             @OA\Property(
 *                 property="nik",
 *                 type="string",
 *                 example="1207270009000001",
 *                 description="Nomor Induk KTP"
 *             ),*             
 *             @OA\Property(
 *                 property="upt_id",
 *                 type="string",
 *                 example="1"
 *             ),
 *
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *                 format="email",
 *                 example="userbaru@gmail.com",
 *                 description="Email user, harus unik"
 *             ),
 *
 *             @OA\Property(
 *                 property="no_wa",
 *                 type="string",
 *                 example="081200998877",
 *                 description="Nomor WhatsApp user, wajib dan harus unik"
 *             ),
 *
 *             @OA\Property(
 *                 property="password",
 *                 type="string",
 *                 example="password123",
 *                 description="Password user minimal 6 karakter"
 *             ),
 *
 *            
 *             @OA\Property(
 *                 property="roles",
 *                 description="Role(s) — bisa id (integer), nama (string), atau array dari id/nama",
 *                 oneOf={
 *                     @OA\Schema(type="integer"),
 *                     @OA\Schema(type="string"),
 *                     @OA\Schema(
 *                         type="array",
 *                         @OA\Items(
 *                             oneOf={
 *                                 @OA\Schema(type="integer"),
 *                                 @OA\Schema(type="string")
 *                             }
 *                         )
 *                     )
 *                 },
 *                 example=1
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="Created",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=201),
 *             @OA\Property(property="message", type="string", example="Created"),
 *             @OA\Property(property="data", ref="#/components/schemas/User")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validation Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=422),
 *             @OA\Property(property="message", type="string", example="Validation Error"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 description="Daftar error per-field (array of messages), sesuai format Validator Laravel",
 *                 example={
 *                     "no_wa": {"Nomor WhatsApp sudah terdaftar."},
 *                     "email": {"Email sudah terdaftar."},
 *                     "roles": {"Role yang dipilih tidak ditemukan."}
 *                 }
 *             )
 *         )
 *     )
 * )
 */


public function store(Request $request)
{
$validator = Validator::make(
    $request->all(),
    [
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|max:255|unique:users,email',
        'no_wa'    => 'required|string|max:255|unique:users,no_wa',
        'nik'    => 'required|string|max:20|unique:users,nik',
        'password' => 'required|string|min:6',
        'roles'      => 'required|integer|exists:roles,id',
        'upt_id'   => 'required|integer|exists:master_upt,id',
    ],
    [
        'name.required'         => 'Nama wajib diisi.',
        'email.required'        => 'Email wajib diisi.',
        'email.email'           => 'Format email tidak valid.',
        'email.unique'          => 'Email sudah terdaftar.',

        'no_wa.required'        => 'Nomor WhatsApp wajib diisi.',
        'no_wa.unique'          => 'Nomor WhatsApp sudah terdaftar.',

        'password.required'     => 'Password wajib diisi.',
        'password.min'          => 'Password minimal 6 karakter.',

        'roles.required'    => 'Role wajib dipilih.',
        'roles.integer'     => 'Role tidak valid.',
        'roles.exists'      => 'Role yang dipilih tidak ditemukan.',
    ]
);

if ($validator->fails()) {
    return response()->json([
        'status'  => 422,
        'message' => 'Validation Error',
        'errors'  => $validator->errors(),
    ], 422);
}




    // create user dulu
$data = new User;
$data->name = $request->name;
$data->no_wa = $request->no_wa;
$data->email = $request->email;
$data->nik = $request->nik;
$data->upt_id = $request->upt_id;
$data->password = Hash::make($request->password);
$data->save();

// normalisasi input roles — bisa berupa id (int), nama (string), atau array
$rolesInput = $request->input('roles');

// ubah menjadi array
$rolesArr = is_array($rolesInput) ? $rolesInput : [$rolesInput];

$rolesToAssign = [];
foreach ($rolesArr as $r) {
    if (is_numeric($r)) {
        // cari Role berdasarkan id
        $roleModel = Role::find($r);
        if ($roleModel) {
            $rolesToAssign[] = $roleModel; // model diterima oleh assignRole
        } else {
            // tangani jika ID tidak ditemukan
            // batalkan dan kembalikan error
            return response()->json([
                'status' => false,
                'message' => "Role dengan id {$r} tidak ditemukan."
            ], 422);
        }
    } else {
        // anggap string = name; periksa ada atau tidak
        $roleModel = Role::where('name', $r)->first();
        if ($roleModel) {
            $rolesToAssign[] = $roleModel;
        } else {
            return response()->json([
                'status' => false,
                'message' => "Role dengan nama '{$r}' tidak ditemukan."
            ], 422);
        }
    }
}

// assign role(s) — bisa menerima model atau nama
$data->assignRole($rolesToAssign);

    Cache::forget("user_me_{$data->id}");
    app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

// =============== LOG ACTIVITY ===============
activity()
->useLog('user_management')            // kategori log
->causedBy(auth()->user())             // siapa yg melakukan
->performedOn($data)                   // objek yg dibuat
->event('created')                     // tipe operasi
->withProperties([
    'old' => null,
    'new' => [
        'name'     => $data->name,
        'email'    => $data->email,
        'no_wa'    => $data->no_wa,
    ],

    // ===== Tambahan detail lengkap =====
    'meta' => [
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
        'url'         => request()->fullUrl(),
        'created_by'  => auth()->user()?->name,
        'created_by_id' => auth()->id(),
        'time'        => now()->format('Y-m-d H:i:s'),
        'note'        => 'User berhasil dibuat melalui endpoint API user.store',
    ]
])
->log("Membuat user baru: {$data->name} ({$data->email})");


return response()->json([
    'status'  => 201,
    'message' => 'Created',
    'data'    => $data
], 201);
}


   /**
 * @OA\Get(
 *     path="/api/users/{id}",
 *     summary="Detail User berdasarkan UUID",
 *     tags={"User"},
 *     security={{"Bearer":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="UUID User",
 *         @OA\Schema(type="string", example="e92f7af6-7399-4c16-a7a5-e48773bb3b8f")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Success"),
 *             @OA\Property(property="data", ref="#/components/schemas/User")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Not Found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="Data tidak ditemukan")
 *         )
 *     )
 * )
 */
public function show($id)
{
    $data = User::with('roles')->find($id);

    if (!$data) {
        return response()->json([
            'status'  => 404,
            'message' => 'Data tidak ditemukan',
        ], 404);
    }

    return response()->json([
        'status'  => 200,
        'message' => 'Success',
        'data'    => $data
    ], 200);
}



    /**
 * @OA\Put(
 *     path="/api/users/{id}",
 *     summary="Update data User",
 *     tags={"User"},
 *     security={{"Bearer":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="UUID User",
 *         @OA\Schema(type="string", example="e92f7af6-7399-4c16-a7a5-e48773bb3b8f")
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","no_wa"},
 *
 *             @OA\Property(property="name", type="string", example="Super Admin"),
 *             @OA\Property(property="email", type="string", example="admin@example.com"),
 *             @OA\Property(property="no_wa", type="string", example="081200998877"),
 *             @OA\Property(property="nik", type="string", example="12072707000000000"),
 *             @OA\Property(property="upt_id", type="integer", example="1"),
 *             @OA\Property(property="password", type="string", example="passwordBaru123", nullable=true)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Updated"),
 *             @OA\Property(property="data", ref="#/components/schemas/User")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Not Found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="Data tidak ditemukan")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validation Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=422),
 *             @OA\Property(property="message", type="string", example="Validation Error"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "email": {"Email sudah terdaftar."},
 *                     "no_wa": {"Nomor WhatsApp sudah terdaftar."}
 *                 }
 *             )
 *         )
 *     )
 * )
 */
public function update(Request $request, $id)
{
    $data = User::find($id);

    if (!$data) {
        return response()->json([
            'status'  => 404,
            'message' => 'Data tidak ditemukan',
        ], 404);
    }

    // ================= VALIDASI =================
    $validator = Validator::make(
        $request->all(),
        [
            'name'     => 'required|string|max:255',
            'email'    => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'no_wa' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'no_wa')->ignore($id),
            ],
            'password' => 'nullable|string|min:6',
            'roles'    => 'required', // boleh id atau nama
        ],
        [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah terdaftar.',
            'no_wa.required' => 'Nomor WhatsApp wajib diisi.',
            'no_wa.unique'   => 'Nomor WhatsApp sudah terdaftar.',
            'nik.required' => 'Nik wajib diisi.',
            'nik.unique'   => 'Nik sudah terdaftar.',
            'password.min'   => 'Password minimal 6 karakter.',
            'roles.required' => 'Role wajib dipilih.',
        ]
    );

    if ($validator->fails()) {
        return response()->json([
            'status'  => 422,
            'message' => 'Validation Error',
            'errors'  => $validator->errors(),
        ], 422);
    }

    // Backup data lama
    $oldData = $data->replicate();

    // ================= UPDATE DATA USER =================
    $data->name  = $request->name;
    $data->email = $request->email;
    $data->no_wa = $request->no_wa;
    $data->upt_id = $request->upt_id;
    $data->nik = $request->nik;

    if ($request->password) {
        $data->password = bcrypt($request->password);
    }

    $data->save();

    // ================= UPDATE ROLE =================
    $rolesInput = $request->input('roles');
    $rolesArr = is_array($rolesInput) ? $rolesInput : [$rolesInput];
    $rolesToAssign = [];

    foreach ($rolesArr as $r) {
        if (is_numeric($r)) {
            $roleModel = Role::find($r);
            if (!$roleModel) {
                return response()->json([
                    'status' => false,
                    'message' => "Role dengan id {$r} tidak ditemukan."
                ], 422);
            }
            $rolesToAssign[] = $roleModel;
        } else {
            $roleModel = Role::where('name', $r)->first();
            if (!$roleModel) {
                return response()->json([
                    'status' => false,
                    'message' => "Role dengan nama '{$r}' tidak ditemukan."
                ], 422);
            }
            $rolesToAssign[] = $roleModel;
        }
    }

    // Replace all old roles → assign new
    $data->syncRoles($rolesToAssign);

    Cache::forget("user_me_{$data->id}");
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

    // =============== LOG ACTIVITY ===============
    activity()
        ->useLog('user_management')
        ->causedBy(auth()->user())
        ->performedOn($data)
        ->event('updated')
        ->withProperties([
            'old' => [
                'name'  => $oldData->name,
                'email' => $oldData->email,
                'no_wa' => $oldData->no_wa,
                'roles' => $oldData->roles->pluck('name'), // tambahan
            ],
            'new' => [
                'name'  => $data->name,
                'email' => $data->email,
                'no_wa' => $data->no_wa,
                'roles' => $data->roles->pluck('name'), // tambahan
            ],
            'meta' => [
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
                'url'           => request()->fullUrl(),
                'updated_by'    => auth()->user()?->name,
                'updated_by_id' => auth()->id(),
                'time'          => now()->format('Y-m-d H:i:s'),
                'note'          => 'User berhasil diperbarui melalui endpoint API user.update',
            ]
        ])
        ->log("Mengupdate user: {$oldData->name} → {$data->name}");

    return response()->json([
        'status'  => 200,
        'message' => 'Updated',
        'data'    => $data
    ], 200);
}


/**
 * @OA\Delete(
 *     path="/api/users/{id}",
 *     summary="Hapus User",
 *     tags={"User"},
 *     security={{"Bearer":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="UUID User yang akan dihapus",
 *         @OA\Schema(type="string", example="e92f7af6-7399-4c16-a7a5-e48773bb3b8f")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil dihapus",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Berhasil menghapus data")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Data Not Found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="Data tidak ditemukan")
 *         )
 *     )
 * )
 */

   public function destroy($id)
{
    $data = User::find($id);

    if (!$data) {
        return response()->json([
            'status'  => 404,
            'message' => 'Data tidak ditemukan'
        ], 404);
    }

    $oldData = $data->replicate();


    $data->delete();

    // ======================= LOG ACTIVITY =======================
    activity()
    ->useLog('user_management')
    ->causedBy(auth()->user())
    ->performedOn($oldData)   // subject = data yang dihapus
    ->event('deleted')
    ->withProperties([
        'old' => [
            'name'  => $oldData->name,
            'email' => $oldData->email,
            'no_wa' => $oldData->no_wa,
        ],
        'new' => null,
        'meta' => [
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
            'url'           => request()->fullUrl(),
            'deleted_by'    => auth()->user()?->name,
            'deleted_by_id' => auth()->id(),
            'time'          => now()->format('Y-m-d H:i:s'),
            'note'          => 'User berhasil dihapus melalui endpoint API user.destroy',
        ]
    ])
    ->log("Menghapus user: {$oldData->name}");

    return response()->json([
        'status'  => 200,
        'message' => 'Berhasil menghapus data'
    ], 200);
}

}
