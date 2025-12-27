<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kategori;
use DataTables;
use Validator;

/**
 * @OA\Tag(
 *     name="Kategori",
 *     description="API untuk manajemen Kategori"
 * )
 */
class KategoriController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:kategori.list',   ['only' => ['index','getData']]);
        $this->middleware('permission:kategori.create', ['only' => ['store']]);
        $this->middleware('permission:kategori.show',   ['only' => ['show']]);
        $this->middleware('permission:kategori.update', ['only' => ['update']]);
        $this->middleware('permission:kategori.delete', ['only' => ['destroy']]);
    }

    /**
     * @OA\Schema(
     *     schema="Kategori",
     *     type="object",
     *     required={"id","nm_kategori"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="nm_kategori", type="string", example="Pengaduan Jalan"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/kategori",
     *     tags={"Kategori"},
     *     summary="List Kategori (pagination & search)",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $search  = $request->search;
        $perPage = $request->per_page ?? 10;
        $page    = $request->page ?? 1;

        $query = Kategori::query();

        if ($search) {
            $query->where('nm_kategori', 'ILIKE', "%{$search}%");
        }

        $paginator = $query
            ->orderBy('id', 'desc')
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
     *     path="/api/kategori/datatables",
     *     tags={"Kategori"},
     *     summary="List Kategori (DataTables)",
     *     security={{"Bearer":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function getData(Request $request)
    {
        $query = Kategori::orderBy('id', 'desc');

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where('nm_kategori', 'LIKE', "%{$search}%");
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('created_at', function ($row) {
                return '<span class="badge badge-info">'
                    . e(optional($row->created_at)->format('Y-m-d H:i'))
                    . '</span>';
            })
            ->addColumn('action', function () {
                return '
                    <button class="btn btn-sm btn-primary">Edit</button>
                    <button class="btn btn-sm btn-danger btn-delete">Hapus</button>
                ';
            })
            ->rawColumns(['created_at','action'])
            ->make(true);
    }

    /**
     * @OA\Post(
     *     path="/api/kategori",
     *     tags={"Kategori"},
     *     summary="Tambah Kategori",
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nm_kategori"},
     *             @OA\Property(property="nm_kategori", type="string", example="Drainase")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nm_kategori' => 'required|string|max:255|unique:master_kategori,nm_kategori',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => 'Validation Error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = Kategori::create([
            'nm_kategori' => $request->nm_kategori,
        ]);

        activity()
            ->useLog('master_kategori_management')
            ->causedBy(auth()->user())
            ->performedOn($data)
            ->event('created')
            ->log('Menambahkan Kategori');

        return response()->json([
            'status'  => 201,
            'message' => 'Created',
            'data'    => $data
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/kategori/{id}",
     *     tags={"Kategori"},
     *     summary="Detail Kategori",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show($id)
    {
        $data = Kategori::find($id);

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
     * @OA\Put(
     *     path="/api/kategori/{id}",
     *     tags={"Kategori"},
     *     summary="Update Kategori",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nm_kategori"},
     *             @OA\Property(property="nm_kategori", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $data = Kategori::find($id);

        if (!$data) {
            return response()->json([
                'status'  => 404,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nm_kategori' => 'required|string|max:255|unique:master_kategori,nm_kategori,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => 'Validation Error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $oldData = $data->replicate();

        $data->update([
            'nm_kategori' => $request->nm_kategori,
        ]);

        activity()
            ->useLog('master_kategori_management')
            ->causedBy(auth()->user())
            ->performedOn($data)
            ->event('updated')
            ->withProperties([
                'old' => $oldData,
                'new' => $data
            ])
            ->log('Mengupdate Kategori');

        return response()->json([
            'status'  => 200,
            'message' => 'Updated',
            'data'    => $data
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/kategori/{id}",
     *     tags={"Kategori"},
     *     summary="Hapus Kategori",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted")
     * )
     */
    public function destroy($id)
    {
        $data = Kategori::find($id);

        if (!$data) {
            return response()->json([
                'status'  => 404,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $oldData = $data->replicate();
        $data->delete();

        activity()
            ->useLog('master_kategori_management')
            ->causedBy(auth()->user())
            ->performedOn($oldData)
            ->event('deleted')
            ->log('Menghapus Kategori');

        return response()->json([
            'status'  => 200,
            'message' => 'Berhasil menghapus data'
        ], 200);
    }
}
