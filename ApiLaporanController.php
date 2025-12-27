<?php

namespace App\Http\Controllers\API\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use DataTables;
use Validator;

/**
 * @OA\Tag(
 *     name="Laporan",
 *     description="API untuk manajemen data Laporan Masyarakat"
 * )
 */
class LaporanController extends Controller
{
    /**
 * @OA\Schema(
 *     schema="Laporan",
 *     type="object",
 *     required={"id","kategori_laporan_id","deskripsi","alamat","status_laporan"},
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="file_masyarakat", type="string", nullable=true),
 *     @OA\Property(property="kategori_laporan_id", type="integer", example=1),
 *     @OA\Property(property="deskripsi", type="string", example="Jalan rusak parah"),
 *     @OA\Property(property="kecamatan_id", type="integer", example=12),
 *     @OA\Property(property="kelurahan_id", type="integer", example=5),
 *     @OA\Property(property="alamat", type="string", example="Jl. Merdeka No.10"),
 *     @OA\Property(property="latitude", type="number", example=-3.597),
 *     @OA\Property(property="longitude", type="number", example=98.672),
 *     @OA\Property(property="status_laporan", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=15),
 *     @OA\Property(property="verif_id", type="integer", nullable=true),
 *     @OA\Property(property="verif_keterangan", type="string", nullable=true),
 *     @OA\Property(property="verif_file", type="string", nullable=true),
 *     @OA\Property(property="verif_tgl", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="penanganan_id", type="integer", nullable=true),
 *     @OA\Property(property="penanganan_keterangan", type="string", nullable=true),
 *     @OA\Property(property="penanganan_tgl", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:laporan.list',   ['only' => ['index','getData']]);
        $this->middleware('permission:laporan.create', ['only' => ['store']]);
        $this->middleware('permission:laporan.show',   ['only' => ['show']]);
        $this->middleware('permission:laporan.update', ['only' => ['update']]);
        $this->middleware('permission:laporan.delete', ['only' => ['destroy']]);
    }

    /**
 * @OA\Get(
 *     path="/api/laporan",
 *     tags={"Laporan"},
 *     summary="List laporan dengan pagination & search",
 *     security={{"Bearer":{}}},
 *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
 *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Laporan")
 *             )
 *         )
 *     )
 * )
 */

    public function index(Request $request)
    {
        $search  = $request->search;
        $perPage = $request->per_page ?? 10;
        $page    = $request->page ?? 1;

        $query = Laporan::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'ILIKE', "%{$search}%")
                  ->orWhere('alamat', 'ILIKE', "%{$search}%");
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')
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
                'search'      => $search,
            ],
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/laporan/datatables",
     *     summary="List laporan untuk DataTables",
     *     tags={"Laporan"},
     *     security={{"Bearer":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function getData(Request $request)
    {
        $query = Laporan::orderBy('created_at', 'desc');

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'LIKE', "%{$search}%")
                  ->orWhere('alamat', 'LIKE', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()

            ->addColumn('status_laporan', function ($row) {
                return match ((int)$row->status_laporan) {
                    1 => '<span class="badge badge-warning">Pending</span>',
                    2 => '<span class="badge badge-success">Disetujui</span>',
                    3 => '<span class="badge badge-danger">Ditolak</span>',
                    default => '-'
                };
            })

            ->addColumn('created_at', function ($row) {
                return '<span class="badge badge-info">'
                    . e(optional($row->created_at)->format('Y-m-d H:i'))
                    . '</span>';
            })

            ->addColumn('action', function ($row) {
                return '<a href="#" class="btn btn-sm btn-primary">Detail</a>
                        <button class="btn btn-sm btn-danger btn-delete">Hapus</button>';
            })

            ->rawColumns(['status_laporan','created_at','action'])
            ->make(true);
    }


 /**
 * @OA\Post(
 *     path="/api/laporan",
 *     summary="Tambah Laporan Masyarakat",
 *     tags={"Laporan"},
 *     security={{"Bearer":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={
 *                     "kategori_laporan_id",
 *                     "deskripsi",
 *                     "alamat"
 *                 },
 *
 *                 @OA\Property(
 *                     property="kategori_laporan_id",
 *                     type="integer",
 *                     enum={1,2,3,4,5},
 *                     example=1,
 *                     description="Kategori Laporan: 
 *                     1 = Jalan Rusak,
 *                     2 = Drainase Tersumbat,
 *                     3 = Banjir,
 *                     4 = Tanggul / Jembatan Rusak,
 *                     5 = Infrastruktur Lainnya"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="deskripsi",
 *                     type="string",
 *                     example="Jalan berlubang sepanjang ±20 meter dan membahayakan pengendara"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="alamat",
 *                     type="string",
 *                     example="Jl. Sudirman No. 45, Kecamatan Lubuk Pakam"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="kecamatan_id",
 *                     type="integer",
 *                     example=5,
 *                     nullable=true
 *                 ),
 *
 *                 @OA\Property(
 *                     property="kelurahan_id",
 *                     type="integer",
 *                     example=12,
 *                     nullable=true
 *                 ),
 *
 *                 @OA\Property(
 *                     property="latitude",
 *                     type="string",
 *                     example="-3.597812",
 *                     nullable=true
 *                 ),
 *
 *                 @OA\Property(
 *                     property="longitude",
 *                     type="string",
 *                     example="98.678921",
 *                     nullable=true
 *                 ),
 *
 *                 @OA\Property(
 *                     property="file_masyarakat",
 *                     type="string",
 *                     format="binary",
 *                     nullable=true,
 *                     description="Foto / video laporan dari masyarakat"
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="Laporan berhasil dibuat"
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validation Error"
 *     )
 * )
 */

 public function store(Request $request)
 {
     $validator = Validator::make($request->all(), [
         'file_masyarakat'     => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
         'kategori_laporan_id' => 'required|in:1,2,3,4,5',
         'deskripsi'           => 'required|string',
         'kecamatan_id'        => 'required|integer',
         'kelurahan_id'        => 'required|integer',
         'alamat'              => 'required|string',
         'latitude'            => 'nullable|numeric|between:-90,90',
         'longitude'           => 'nullable|numeric|between:-180,180',
     ], [
         'file_masyarakat.file' => 'File tidak valid',
         'file_masyarakat.mimes' => 'Format file harus jpg, png',
         'file_masyarakat.max' => 'Ukuran file maksimal 2MB',
     ]);
 
     if ($validator->fails()) {
         return response()->json([
             'status'  => 422,
             'message' => 'Validation Error',
             'errors'  => $validator->errors(),
         ], 422);
     }
 
     // ================= FILE UPLOAD =================
     $fileName = null;

    if ($request->hasFile('file_masyarakat')) {
        $file = $request->file('file_masyarakat');

        // bikin nama file unik & aman
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // simpan ke storage
        $file->storeAs('laporan/masyarakat', $fileName, 'public');
    }
 
     // ================= INSERT DATA =================
     $data = Laporan::create([
         'file_masyarakat'     => $fileName,
         'kategori_laporan_id' => $request->kategori_laporan_id,
         'deskripsi'           => $request->deskripsi,
         'kecamatan_id'        => $request->kecamatan_id,
         'kelurahan_id'        => $request->kelurahan_id,
         'alamat'              => $request->alamat,
         'latitude'            => $request->latitude,
         'longitude'           => $request->longitude,
         'status_laporan'      => 0,
         'user_id'             => auth()->id(), // 🔥 dari user login
     ]);
 
     // ================= LOG ACTIVITY =================
     activity()
         ->useLog('laporan_management')
         ->causedBy(auth()->user())
         ->performedOn($data)
         ->event('created')
         ->withProperties([
             'user_id' => auth()->id(),
             'file'    => $fileName,
         ])
         ->log('Membuat laporan masyarakat');
 
     return response()->json([
         'status'  => 201,
         'message' => 'Created',
         'data'    => $data,
     ], 201);
 }
 

    /**
 * @OA\Get(
 *     path="/api/laporan/{id}",
 *     tags={"Laporan"},
 *     summary="Detail laporan",
 *     security={{"Bearer":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(ref="#/components/schemas/Laporan")
 *     ),
 *     @OA\Response(response=404, description="Not Found")
 * )
 */

    public function show($id)
    {
        $data = Laporan::find($id);

        if (!$data) {
            return response()->json([
                'status'  => 404,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Success',
            'data'    => $data
        ], 200);
    }

    /**
 * @OA\Post(
 *     path="/api/laporan/{id}",
 *     tags={"Laporan"},
 *     summary="Update laporan (upload ulang file)",
 *     security={{"Bearer":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="UUID Laporan",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={
 *                     "kategori_laporan_id",
 *                     "deskripsi",
 *                     "kecamatan_id",
 *                     "kelurahan_id",
 *                     "alamat"
 *                 },
 *
 *                 @OA\Property(property="file_masyarakat", type="string", format="binary"),
 *
 *                 @OA\Property(
 *                     property="kategori_laporan_id",
 *                     type="integer",
 *                     enum={1,2,3,4,5},
 *                     description="
 *                      1 = Jalan Rusak  
 *                      2 = Drainase Tersumbat  
 *                      3 = Banjir  
 *                      4 = Tanggul / Jembatan Rusak  
 *                      5 = Infrastruktur Lainnya"
 *                 ),
 *
 *                 @OA\Property(property="deskripsi", type="string"),
 *                 @OA\Property(property="kecamatan_id", type="integer"),
 *                 @OA\Property(property="kelurahan_id", type="integer"),
 *                 @OA\Property(property="alamat", type="string"),
 *                 @OA\Property(property="latitude", type="number"),
 *                 @OA\Property(property="longitude", type="number")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Updated",
 *         @OA\JsonContent(ref="#/components/schemas/Laporan")
 *     )
 * )
 */
 public function update(Request $request, $id)
 {
    $data = Laporan::find($id);
 
     if (!$data) {
         return response()->json([
             'status'  => 404,
             'message' => 'Data tidak ditemukan'
         ], 404);
        }
 
     // ================= VALIDASI =================
     $validator = Validator::make($request->all(), [
         'file_masyarakat'     => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
         'kategori_laporan_id' => 'required|integer|in:1,2,3,4,5',
         'deskripsi'           => 'required|string',
         'kecamatan_id'        => 'required|integer',
         'kelurahan_id'        => 'required|integer',
         'alamat'              => 'required|string',
         'latitude'            => 'nullable|numeric|between:-90,90',
         'longitude'           => 'nullable|numeric|between:-180,180',
     ]);
 
     if ($validator->fails()) {
         return response()->json([
             'status'  => 422,
             'message' => 'Validation Error',
             'errors'  => $validator->errors(),
         ], 422);
     }
 
     $oldData = $data->replicate();
 
     // ================= FILE HANDLING =================
     $fileName = $data->file_masyarakat;
 
     if ($request->hasFile('file_masyarakat')) {
 
         // hapus file lama
         if ($data->file_masyarakat &&
             Storage::disk('public')->exists('laporan/masyarakat/' . $data->file_masyarakat)) {
             Storage::disk('public')->delete('laporan/masyarakat/' . $data->file_masyarakat);
         }
 
         // simpan file baru
         $file = $request->file('file_masyarakat');
         $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
         $file->storeAs('laporan/masyarakat', $fileName, 'public');
     }
 
     // ================= UPDATE DATA =================
     $data->update([
         'file_masyarakat'     => $fileName,
         'kategori_laporan_id' => $request->kategori_laporan_id,
         'deskripsi'           => $request->deskripsi,
         'kecamatan_id'        => $request->kecamatan_id,
         'kelurahan_id'        => $request->kelurahan_id,
         'alamat'              => $request->alamat,
         'latitude'            => $request->latitude,
         'longitude'           => $request->longitude,
     ]);
 
     // ================= LOG ACTIVITY =================
     activity()
         ->useLog('laporan_management')
         ->causedBy(auth()->user())
         ->performedOn($data)
         ->event('updated')
         ->withProperties([
             'old' => $oldData,
             'new' => $data,
         ])
         ->log('Mengupdate laporan + upload ulang file');
 
     return response()->json([
         'status'  => 200,
         'message' => 'Updated',
         'data'    => $data
     ], 200);
 }
 

    /**
 * @OA\Delete(
 *     path="/api/laporan/{id}",
 *     tags={"Laporan"},
 *     summary="Hapus laporan",
 *     security={{"Bearer":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *     @OA\Response(response=200, description="Deleted")
 * )
 */

    public function destroy($id)
    {
        $data = Laporan::find($id);

        if (!$data) {
            return response()->json([
                'status'  => 404,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $oldData = $data->replicate();
        $data->delete();

        activity()
            ->useLog('laporan_management')
            ->causedBy(auth()->user())
            ->performedOn($oldData)
            ->event('deleted')
            ->log("Menghapus laporan");

        return response()->json([
            'status'  => 200,
            'message' => 'Berhasil menghapus data'
        ], 200);
    }

    // =========================================================|
//                         ADMIN UPT                        |
// =========================================================|
    /**
 * @OA\Post(
 *     path="/api/laporan/verifikasi/{id}",
 *     tags={"Admin"},
 *     summary="Verifikasi Laporan",
 *     security={{"Bearer":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="UUID Laporan",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"verif_keterangan"},
 *
 *                 @OA\Property(
 *                     property="verif_keterangan",
 *                     type="string",
 *                     example="Laporan telah diverifikasi dan disetujui"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="verif_file",
 *                     type="string",
 *                     format="binary",
 *                     nullable=true
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Laporan berhasil diverifikasi"
 *     ),
 *     @OA\Response(response=404, description="Data tidak ditemukan"),
 *     @OA\Response(response=422, description="Validation Error")
 * )
 */
public function verifUpt(Request $request, $id)
{
    $data = Laporan::find($id);

    if (!$data) {
        return response()->json([
            'status'  => 404,
            'message' => 'Data tidak ditemukan'
        ], 404);
    }

    // ================= VALIDASI =================
    $validator = Validator::make($request->all(), [
        'verif_keterangan' => 'required|string',
        'verif_file'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ], [
        'verif_keterangan.required' => 'Keterangan verifikasi wajib diisi',
        'verif_file.mimes'          => 'File harus berupa pdf, jpg, jpeg, atau png',
        'verif_file.max'            => 'Ukuran file maksimal 2MB',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 422,
            'message' => 'Validation Error',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $oldData = $data->replicate();

    // ================= FILE HANDLING =================
    $fileName = $data->verif_file;

    if ($request->hasFile('verif_file')) {

        // hapus file lama jika ada
        if ($data->verif_file &&
            Storage::disk('public')->exists('verifikasi_laporan/' . $data->verif_file)) {
            Storage::disk('public')->delete('verifikasi_laporan/' . $data->verif_file);
        }

        $file = $request->file('verif_file');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $file->storeAs('verifikasi_laporan', $fileName, 'public');
    }

    // ================= UPDATE DATA =================
    $data->update([
        'verif_id'         => auth()->id(),
        'verif_keterangan' => $request->verif_keterangan,
        'verif_file'       => $fileName,   // 🔥 hanya nama file
        'verif_tgl'        => now(),
        'status_laporan'   => 2,            // ✅ DISSETUJUI
    ]);

    // ================= LOG ACTIVITY =================
    activity()
        ->useLog('laporan_management')
        ->causedBy(auth()->user())
        ->performedOn($data)
        ->event('verified')
        ->withProperties([
            'old' => $oldData,
            'new' => $data,
        ])
        ->log('Memverifikasi laporan');

    return response()->json([
        'status'  => 200,
        'message' => 'Laporan berhasil diverifikasi',
        'data'    => $data
    ], 200);
}


/**
 * @OA\Post(
 *     path="/api/laporan/veriftolak/{id}",
 *     tags={"Admin"},
 *     summary="Tolak Laporan",
 *     security={{"Bearer":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="UUID Laporan",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"verif_keterangan_tolak"},
 *
 *                 @OA\Property(
 *                     property="verif_keterangan_tolak",
 *                     type="string",
 *                     example="Laporan telah diverifikasi dan disetujui"
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Laporan berhasil diverifikasi"
 *     ),
 *     @OA\Response(response=404, description="Data tidak ditemukan"),
 *     @OA\Response(response=422, description="Validation Error")
 * )
 */
    public function TolakUpt(Request $request, $id)
    {
        $data = Laporan::find($id);

        if (!$data) {
            return response()->json([
                'status'  => 404,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // ================= VALIDASI =================
        $validator = Validator::make($request->all(), [
            'verif_keterangan_tolak' => 'required|string',
        ], [
            'verif_keterangan_tolak.required' => 'Keterangan verifikasi wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => 'Validation Error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $oldData = $data->replicate();

        // ================= UPDATE DATA =================
        $data->update([
            'verif_id'         => auth()->id(),
            'verif_keterangan_tolak' => $request->verif_keterangan_tolak,
            'verif_tgl_tolak'        => now(),
            'status_laporan'   => 5, // ✅ TOLAK
        ]);

        // ================= LOG ACTIVITY =================
        activity()
            ->useLog('laporan_management')
            ->causedBy(auth()->user())
            ->performedOn($data)
            ->event('verified')
            ->withProperties([
                'old' => $oldData,
                'new' => $data,
            ])
            ->log('Tolak Verifikasi');

        return response()->json([
            'status'  => 200,
            'message' => 'Laporan berhasil diverifikasi',
            'data'    => $data
        ], 200);
    }


    /**
 * @OA\Post(
 *     path="/api/laporan/penerima/{id}",
 *     tags={"Admin"},
 *     summary="Terima Laporan dan Tentukan UPT",
 *     security={{"Bearer":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="UUID Laporan",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={
 *                     "penerima_keterangan",
 *                     "upt_id"
 *                 },
 *
 *                 @OA\Property(
 *                     property="penerima_keterangan",
 *                     type="string",
 *                     example="Laporan telah diterima dan diteruskan ke UPT terkait"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="upt_id",
 *                     type="integer",
 *                     example=3,
 *                     description="ID UPT penerima laporan"
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Laporan berhasil diterima dan diteruskan ke UPT"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation Error"
 *     )
 * )
 */
public function penerima(Request $request, $id)
{
    $data = Laporan::find($id);

    if (!$data) {
        return response()->json([
            'status'  => 404,
            'message' => 'Data tidak ditemukan'
        ], 404);
    }

    // ================= VALIDASI =================
    $validator = Validator::make($request->all(), [
        'penerima_keterangan' => 'required|string',
    ], [
        'penerima_keterangan.required' => 'Keterangan verifikasi wajib diisi',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 422,
            'message' => 'Validation Error',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $oldData = $data->replicate();

    // ================= UPDATE DATA =================
    $data->update([
        'penerima_id'         => auth()->id(),
        'penerima_keterangan' => $request->penerima_keterangan,
        'penerima_tgl'        => now(),
        'upt_id'              => $request->upt_id,
        'status_laporan'      => 1, // ✅ Di Terima
    ]);

    // ================= LOG ACTIVITY =================
    activity()
        ->useLog('laporan_management')
        ->causedBy(auth()->user())
        ->performedOn($data)
        ->event('verified')
        ->withProperties([
            'old' => $oldData,
            'new' => $data,
        ])
        ->log('Menerima Laporan');

    return response()->json([
        'status'  => 200,
        'message' => 'Laporan berhasil diverifikasi',
        'data'    => $data
    ], 200);
}

/**
 * @OA\Post(
 *     path="/api/laporan/penerimatolak/{id}",
 *     tags={"Admin"},
 *     summary="Tolak Laporan",
 *     security={{"Bearer":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="UUID Laporan",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={
 *                     "penerima_keterangan_tolak"
 *                 },
 *
 *                 @OA\Property(
 *                     property="penerima_keterangan_tolak",
 *                     type="string",
 *                     example="Laporan telah Tidak Sesuai"
 *                 ),
 *
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Laporan berhasil diterima dan diteruskan ke UPT"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation Error"
 *     )
 * )
 */
    public function Tolakpenerima(Request $request, $id)
    {
        $data = Laporan::find($id);

        if (!$data) {
            return response()->json([
                'status'  => 404,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // ================= VALIDASI =================
        $validator = Validator::make($request->all(), [
            'penerima_keterangan_tolak' => 'required|string',
        ], [
            'penerima_keterangan_tolak.required' => 'Keterangan verifikasi wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => 'Validation Error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $oldData = $data->replicate();

        // ================= UPDATE DATA =================
        $data->update([
            'penerima_id'         => auth()->id(),
            'penerima_keterangan_tolak' => $request->penerima_keterangan_tolak,
            'penerima_tgl_tolak'        => now(),
            'status_laporan'            => 5, // ✅ Di Tolak
        ]);

        // ================= LOG ACTIVITY =================
        activity()
            ->useLog('laporan_management')
            ->causedBy(auth()->user())
            ->performedOn($data)
            ->event('verified')
            ->withProperties([
                'old' => $oldData,
                'new' => $data,
            ])
            ->log('Tolak Laporan Penerima');

        return response()->json([
            'status'  => 200,
            'message' => 'Laporan berhasil diverifikasi',
            'data'    => $data
        ], 200);
    }


    /**
 * @OA\Post(
 *     path="/api/laporan/penanganan/{id}",
 *     tags={"Admin"},
 *     summary="Penanganan Laporan",
 *     security={{"Bearer":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="UUID Laporan",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"penanganan_keterangan"},
 *
 *                 @OA\Property(
 *                     property="penanganan_keterangan",
 *                     type="string",
 *                     example="Laporan telah diverifikasi dan disetujui"
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Laporan berhasil diverifikasi"
 *     ),
 *     @OA\Response(response=404, description="Data tidak ditemukan"),
 *     @OA\Response(response=422, description="Validation Error")
 * )
 */
public function penanganan(Request $request, $id)
{
    $data = Laporan::find($id);

    if (!$data) {
        return response()->json([
            'status'  => 404,
            'message' => 'Data tidak ditemukan'
        ], 404);
    }

    // ================= VALIDASI =================
    $validator = Validator::make($request->all(), [
        'penanganan_keterangan' => 'required|string',
    ], [
        'penanganan_keterangan.required' => 'Keterangan verifikasi wajib diisi',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 422,
            'message' => 'Validation Error',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $oldData = $data->replicate();

    // ================= UPDATE DATA =================
    $data->update([
        'penanganan_id'         => auth()->id(),
        'penanganan_keterangan' => $request->penanganan_keterangan,
        'penanganan_tgl'        => now(),
        'status_laporan'        => 3, // ✅ Penanganan
    ]);

    // ================= LOG ACTIVITY =================
    activity()
        ->useLog('laporan_management')
        ->causedBy(auth()->user())
        ->performedOn($data)
        ->event('verified')
        ->withProperties([
            'old' => $oldData,
            'new' => $data,
        ])
        ->log('Penanganan laporan');

    return response()->json([
        'status'  => 200,
        'message' => 'Laporan berhasil diverifikasi',
        'data'    => $data
    ], 200);
}

/**
 * @OA\Post(
 *     path="/api/laporan/penanganantolak/{id}",
 *     tags={"Admin"},
 *     summary="Penanganan Tolak Laporan",
 *     security={{"Bearer":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="UUID Laporan",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"penanganan_keterangan_tolak"},
 *
 *                 @OA\Property(
 *                     property="penanganan_keterangan_tolak",
 *                     type="string",
 *                     example="Laporan Ditolak"
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Laporan berhasil diverifikasi"
 *     ),
 *     @OA\Response(response=404, description="Data tidak ditemukan"),
 *     @OA\Response(response=422, description="Validation Error")
 * )
 */
public function penangananTolak(Request $request, $id)
{
    $data = Laporan::find($id);

    if (!$data) {
        return response()->json([
            'status'  => 404,
            'message' => 'Data tidak ditemukan'
        ], 404);
    }

    // ================= VALIDASI =================
    $validator = Validator::make($request->all(), [
        'penanganan_keterangan_tolak' => 'required|string',
    ], [
        'penanganan_keterangan_tolak.required' => 'Keterangan verifikasi wajib diisi',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 422,
            'message' => 'Validation Error',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $oldData = $data->replicate();

    // ================= UPDATE DATA =================
    $data->update([
        'penanganan_id'         => auth()->id(),
        'penanganan_keterangan_tolak' => $request->penanganan_keterangan_tolak,
        'penanganan_tgl_tolak'        => now(),
        'status_laporan'        => 5, // ✅ Penanganan
    ]);

    // ================= LOG ACTIVITY =================
    activity()
        ->useLog('laporan_management')
        ->causedBy(auth()->user())
        ->performedOn($data)
        ->event('verified')
        ->withProperties([
            'old' => $oldData,
            'new' => $data,
        ])
        ->log('Penanganan laporan Di Tolak');

    return response()->json([
        'status'  => 200,
        'message' => 'Laporan berhasil diverifikasi',
        'data'    => $data
    ], 200);
}
}
