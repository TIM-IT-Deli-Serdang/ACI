<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterUpt;
use DataTables;
use Validator;

/**
 * @OA\Tag(
 *     name="Upt",
 *     description="API untuk manajemen UPT"
 * )
 */
class UptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:upt.list',   ['only' => ['index','getData']]);
        $this->middleware('permission:upt.create', ['only' => ['store']]);
        $this->middleware('permission:upt.show',   ['only' => ['show']]);
        $this->middleware('permission:upt.update', ['only' => ['update']]);
        $this->middleware('permission:upt.delete', ['only' => ['destroy']]);
    }

    /**
     * @OA\Schema(
     *     schema="MasterUpt",
     *     type="object",
     *     required={"id","nama_upt"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="nama_upt", type="string", example="UPT SDABMB Lubuk Pakam"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/upt",
     *     tags={"Upt"},
     *     summary="List UPT (pagination & search)",
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

        $query = MasterUpt::query();

        if ($search) {
            $query->where('nama_upt', 'ILIKE', "%{$search}%");
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
     *     path="/api/upt/datatables",
     *     tags={"Upt"},
     *     summary="List UPT (DataTables)",
     *     security={{"Bearer":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function getData(Request $request)
    {
        $query = MasterUpt::orderBy('id', 'desc');

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where('nama_upt', 'LIKE', "%{$search}%");
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
     *     path="/api/upt",
     *     tags={"Upt"},
     *     summary="Tambah UPT",
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nama_upt"},
     *             @OA\Property(property="nama_upt", type="string", example="UPT SDABMB Percut Sei Tuan")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_upt' => 'required|string|max:255|unique:master_upt,nama_upt',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => 'Validation Error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = MasterUpt::create([
            'nama_upt' => $request->nama_upt,
        ]);

        activity()
            ->useLog('master_upt_management')
            ->causedBy(auth()->user())
            ->performedOn($data)
            ->event('created')
            ->log('Menambahkan UPT');

        return response()->json([
            'status'  => 201,
            'message' => 'Created',
            'data'    => $data
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/upt/{id}",
     *     tags={"Upt"},
     *     summary="Detail UPT",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show($id)
    {
        $data = MasterUpt::find($id);

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
     *     path="/api/upt/{id}",
     *     tags={"Upt"},
     *     summary="Update UPT",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nama_upt"},
     *             @OA\Property(property="nama_upt", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $data = MasterUpt::find($id);

        if (!$data) {
            return response()->json([
                'status'  => 404,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_upt' => 'required|string|max:255|unique:master_upt,nama_upt,' . $id,
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
            'nama_upt' => $request->nama_upt,
        ]);

        activity()
            ->useLog('master_upt_management')
            ->causedBy(auth()->user())
            ->performedOn($data)
            ->event('updated')
            ->withProperties([
                'old' => $oldData,
                'new' => $data
            ])
            ->log('Mengupdate UPT');

        return response()->json([
            'status'  => 200,
            'message' => 'Updated',
            'data'    => $data
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/upt/{id}",
     *     tags={"Upt"},
     *     summary="Hapus UPT",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted")
     * )
     */
    public function destroy($id)
    {
        $data = MasterUpt::find($id);

        if (!$data) {
            return response()->json([
                'status'  => 404,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $oldData = $data->replicate();
        $data->delete();

        activity()
            ->useLog('master_upt_management')
            ->causedBy(auth()->user())
            ->performedOn($oldData)
            ->event('deleted')
            ->log('Menghapus UPT');

        return response()->json([
            'status'  => 200,
            'message' => 'Berhasil menghapus data'
        ], 200);
    }
}
