<?php

namespace App\Http\Controllers\API\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use Illuminate\Support\Facades\DB;
// use Barryvdh\DomPDF\Facade ;
use Barryvdh\DomPDF\Facade as PDF;


/**
 * @OA\Tag(
 *     name="Rekap Laporan",
 *     description="API Rekap Laporan berdasarkan Status"
 * )
 */
class LaporanRekapController extends Controller
{
    /**
     * Mapping status laporan
     */
    private const STATUS_MAP = [
        0 => 'pengajuan',
        1 => 'diterima',
        2 => 'diverifikasi',
        3 => 'dalam_penanganan',
        4 => 'selesai',
        5 => 'ditolak',
    ];
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:rekap.list',   ['only' => ['index','perTgl','perKec']]);
    }

    /**
     * @OA\Get(
     *     path="/api/rekap",
     *     tags={"Rekap Laporan"},
     *     summary="Get laporan dan rekap jumlah per status",
     *     description="Mengambil data laporan serta jumlah laporan berdasarkan status (0–5)",
     *     security={{"Bearer":{}}},
     *
     *     @OA\Parameter(
     *         name="status_laporan",
     *         in="query",
     *         description="Filter status laporan (0=Pengajuan, 1=Diterima, 2=Diverifikasi, 3=Dalam Penanganan, 4=Selesai, 5=Tolak)",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             enum={0,1,2,3,4,5},
     *             example=0
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Rekap laporan berhasil diambil"),
     *             @OA\Property(
     *                 property="rekap_status",
     *                 type="object",
     *                 @OA\Property(property="pengajuan", type="integer", example=5),
     *                 @OA\Property(property="diterima", type="integer", example=3),
     *                 @OA\Property(property="diverifikasi", type="integer", example=4),
     *                 @OA\Property(property="dalam_penanganan", type="integer", example=2),
     *                 @OA\Property(property="selesai", type="integer", example=7),
     *                 @OA\Property(property="ditolak", type="integer", example=1)
     *             ),
     *             @OA\Property(property="total", type="integer", example=22),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", format="uuid"),
     *                     @OA\Property(property="status_laporan", type="integer", example=0),
     *                     @OA\Property(property="deskripsi", type="string", example="Jalan rusak"),
     *                     @OA\Property(property="alamat", type="string", example="Jl. Merdeka"),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        /* ======================
         * QUERY DASAR
         * ====================== */
        $query = Laporan::query();

        /* ======================
         * FILTER STATUS
         * ====================== */
        if ($request->filled('status_laporan')) {
            $query->where('status_laporan', $request->status_laporan);
        }

        /* ======================
         * DATA LAPORAN
         * ====================== */
        $data = $query
            ->orderBy('created_at', 'desc')
            ->get();

        /* ======================
         * REKAP STATUS RAW
         * ====================== */
        $rekapRaw = Laporan::select(
                'status_laporan',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('status_laporan')
            ->pluck('total', 'status_laporan');

        /* ======================
         * REKAP STATUS MAPPED
         * ====================== */
        $rekapStatus = [];
        foreach (self::STATUS_MAP as $key => $label) {
            $rekapStatus[$label] = $rekapRaw[$key] ?? 0;
        }

        return response()->json([
            'status' => true,
            'message' => 'Rekap laporan berhasil diambil',
            'rekap_status' => $rekapStatus,
            'total' => array_sum($rekapStatus),
            'data' => $data
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/rekap/tanggal",
     *     tags={"Rekap Laporan"},
     *     summary="Get laporan dan jumlah per tanggal",
     *     description="Mengambil data laporan serta rekap jumlah laporan per tanggal (created_at)",
     *     security={{"Bearer":{}}},
     *
     *     @OA\Parameter(
     *         name="tanggal_awal",
     *         in="query",
     *         description="Tanggal awal (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-01-01")
     *     ),
     *
     *     @OA\Parameter(
     *         name="tanggal_akhir",
     *         in="query",
     *         description="Tanggal akhir (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-01-31")
     *     ),
     *
     *     @OA\Parameter(
     *         name="status_laporan",
     *         in="query",
     *         description="Filter status laporan (0–5)",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             enum={0,1,2,3,4,5},
     *             example=0
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Rekap laporan per tanggal berhasil diambil"),
     *
     *             @OA\Property(
     *                 property="rekap_tanggal",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="tanggal", type="string", example="2025-01-10"),
     *                     @OA\Property(property="total", type="integer", example=5)
     *                 )
     *             ),
     *
     *             @OA\Property(
     *                 property="rekap_status",
     *                 type="object",
     *                 @OA\Property(property="pengajuan", type="integer", example=3),
     *                 @OA\Property(property="diterima", type="integer", example=2),
     *                 @OA\Property(property="diverifikasi", type="integer", example=4),
     *                 @OA\Property(property="dalam_penanganan", type="integer", example=1),
     *                 @OA\Property(property="selesai", type="integer", example=6),
     *                 @OA\Property(property="ditolak", type="integer", example=1)
     *             ),
     *
     *             @OA\Property(property="total", type="integer", example=17),
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", format="uuid"),
     *                     @OA\Property(property="status_laporan", type="integer", example=0),
     *                     @OA\Property(property="deskripsi", type="string", example="Jalan rusak"),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function perTgl(Request $request)
    {
        /* ======================
         * QUERY DASAR
         * ====================== */
        $query = Laporan::query();

        /* ======================
         * FILTER TANGGAL
         * ====================== */
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('created_at', '>=', $request->tanggal_awal);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }

        /* ======================
         * FILTER STATUS (OPSIONAL)
         * ====================== */
        if ($request->filled('status_laporan')) {
            $query->where('status_laporan', $request->status_laporan);
        }

        /* ======================
        * DATA LAPORAN
        * ====================== */
        $data = (clone $query)
        ->orderBy('created_at', 'desc')
        ->get();

        /* ======================
        * REKAP PER TANGGAL
        * ====================== */
        $rekapTanggal = (clone $query)
        ->reorder()
        ->select(
            DB::raw('DATE(created_at) as tanggal'),
            DB::raw('COUNT(*) as total')
        )
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('tanggal', 'asc')
        ->get();



        /* ======================
         * REKAP STATUS
         * ====================== */
        $rekapRaw = (clone $query)
            ->select('status_laporan', DB::raw('COUNT(*) as total'))
            ->groupBy('status_laporan')
            ->pluck('total', 'status_laporan');

        $rekapStatus = [];
        foreach (self::STATUS_MAP as $key => $label) {
            $rekapStatus[$label] = $rekapRaw[$key] ?? 0;
        }

        return response()->json([
            'status' => true,
            'message' => 'Rekap laporan per tanggal berhasil diambil',
            'rekap_tanggal' => $rekapTanggal,
            'rekap_status' => $rekapStatus,
            'total' => array_sum($rekapStatus),
            'data' => $data
        ], 200);
    }
     /**
     * @OA\Get(
     *     path="/api/rekap/kecamatan",
     *     tags={"Rekap Laporan"},
     *     summary="Get laporan dan rekap jumlah berdasarkan kecamatan",
     *     security={{"Bearer":{}}},
     *
     *     @OA\Parameter(
     *         name="kecamatan_id",
     *         in="query",
     *         required=true,
     *         description="ID Kecamatan",
     *         @OA\Schema(type="integer", example=320101)
     *     ),
     *
     *     @OA\Parameter(
     *         name="status_laporan",
     *         in="query",
     *         required=false,
     *         description="Status laporan (0–5)",
     *         @OA\Schema(type="integer", enum={0,1,2,3,4,5})
     *     ),
     *
     *
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil"
     *     )
     * )
     */
    public function perKec(Request $request)
    {
        /* ======================
         * VALIDASI
         * ====================== */
        $request->validate([
            'kecamatan_id' => 'required'
        ]);

        /* ======================
         * QUERY DASAR (FILTER KECAMATAN)
         * ====================== */
        $query = Laporan::where('kecamatan_id', $request->kecamatan_id);

        /* ======================
         * FILTER TAMBAHAN
         * ====================== */
        if ($request->filled('status_laporan')) {
            $query->where('status_laporan', $request->status_laporan);
        }

        /* ======================
         * DATA LAPORAN
         * ====================== */
        $data = (clone $query)
            ->orderBy('created_at', 'desc')
            ->get();

        /* ======================
         * REKAP STATUS
         * ====================== */
        $rekapRaw = (clone $query)
            ->reorder() // 🔥 penting (PostgreSQL safe)
            ->select('status_laporan', DB::raw('COUNT(*) as total'))
            ->groupBy('status_laporan')
            ->pluck('total', 'status_laporan');

        $rekapStatus = [];
        foreach (self::STATUS_MAP as $key => $label) {
            $rekapStatus[$label] = $rekapRaw[$key] ?? 0;
        }

        return response()->json([
            'status' => true,
            'message' => 'Data laporan berdasarkan kecamatan berhasil diambil',
            'kecamatan_id' => $request->kecamatan_id,
            'rekap_status' => $rekapStatus,
            'total' => array_sum($rekapStatus),
            'data' => $data
        ], 200);
    }
    /**
     * @OA\Get(
     *     path="/api/rekap/by-kecamatan/pdf",
     *     tags={"Laporan PDF"},
     *     summary="Download PDF rekap laporan berdasarkan kecamatan",
     *     description="Mengunduh laporan rekap jumlah laporan per status berdasarkan kecamatan dalam format PDF",
     *     security={{"Bearer":{}}},
     *
     *     @OA\Parameter(
     *         name="kecamatan_id",
     *         in="query",
     *         required=true,
     *         description="ID Kecamatan",
     *         @OA\Schema(
     *             type="integer",
     *             example=5
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="status_laporan",
     *         in="query",
     *         required=false,
     *         description="Filter status laporan (0=Pengajuan, 1=Diterima, 2=Diverifikasi, 3=Dalam Penanganan, 4=Selesai, 5=Ditolak)",
     *         @OA\Schema(
     *             type="integer",
     *             enum={0,1,2,3,4,5},
     *             example=0
     *         )
     *     ),
     *
     *
     *     @OA\Response(
     *         response=200,
     *         description="PDF berhasil di-generate",
     *         @OA\MediaType(
     *             mediaType="application/pdf",
     *             @OA\Schema(
     *                 type="string",
     *                 format="binary"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function downloadKec(Request $request)
    {
        $request->validate([
            'kecamatan_id' => 'required'
        ]);
    
        $query = Laporan::where('kecamatan_id', $request->kecamatan_id);
    
        $rekapRaw = (clone $query)
            ->reorder()
            ->select('status_laporan', DB::raw('COUNT(*) as total'))
            ->groupBy('status_laporan')
            ->pluck('total', 'status_laporan');
    
        $rekapStatus = [];
        foreach (self::STATUS_MAP as $key => $label) {
            $rekapStatus[] = [
                'label' => $label,
                'total' => $rekapRaw[$key] ?? 0
            ];
        }
    
        $total = array_sum(array_column($rekapStatus, 'total'));
    
        $pdf = app('dompdf.wrapper')->loadView(
            'pdf.rekap-laporan-kecamatan',
            compact('rekapStatus', 'total') + [
                'kecamatan_id' => $request->kecamatan_id,
                'request' => $request
            ]
        );
    
        return $pdf->download('rekap-laporan-kecamatan.pdf');
    }

 /**
 * @OA\Get(
 *     path="/api/rekap/per-tanggal/pdf",
 *     tags={"Laporan PDF"},
 *     summary="Download PDF rekap laporan per tanggal",
 *     description="Mengunduh laporan rekap jumlah laporan per tanggal dan status dalam format PDF",
 *     security={{"Bearer":{}}},
 *
 *     @OA\Parameter(
 *         name="tanggal_awal",
 *         in="query",
 *         required=false,
 *         description="Tanggal awal filter",
 *         @OA\Schema(type="string", format="date", example="2025-01-01")
 *     ),
 *
 *     @OA\Parameter(
 *         name="tanggal_akhir",
 *         in="query",
 *         required=false,
 *         description="Tanggal akhir filter",
 *         @OA\Schema(type="string", format="date", example="2025-01-31")
 *     ),
 *
 *     @OA\Parameter(
 *         name="status_laporan",
 *         in="query",
 *         required=false,
 *         description="Filter status laporan (0=Pengajuan, 1=Diterima, 2=Diverifikasi, 3=Dalam Penanganan, 4=Selesai, 5=Ditolak)",
 *         @OA\Schema(
 *             type="integer",
 *             enum={0,1,2,3,4,5},
 *             example=0
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="PDF berhasil di-generate",
 *         @OA\MediaType(
 *             mediaType="application/pdf",
 *             @OA\Schema(type="string", format="binary")
 *         )
 *     ),
 *
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */
   public function downloadPerTanggal(Request $request)
{
    /* ======================
     * QUERY DASAR
     * ====================== */
    $query = Laporan::query();

    /* ======================
     * FILTER TANGGAL
     * ====================== */
    if ($request->filled('tanggal_awal')) {
        $query->whereDate('created_at', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
        $query->whereDate('created_at', '<=', $request->tanggal_akhir);
    }

    /* ======================
     * FILTER STATUS (OPSIONAL)
     * ====================== */
    if ($request->filled('status_laporan')) {
        $query->where('status_laporan', $request->status_laporan);
    }

    /* ======================
     * DATA LAPORAN
     * ====================== */
    $data = (clone $query)
        ->orderBy('created_at', 'desc')
        ->get();

    /* ======================
     * REKAP PER TANGGAL
     * ====================== */
    $rekapTanggal = (clone $query)
        ->reorder()
        ->select(
            DB::raw('DATE(created_at) as tanggal'),
            DB::raw('COUNT(*) as total')
        )
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('tanggal', 'asc')
        ->get();

    /* ======================
     * REKAP STATUS
     * ====================== */
    $rekapRaw = (clone $query)
        ->reorder()
        ->select('status_laporan', DB::raw('COUNT(*) as total'))
        ->groupBy('status_laporan')
        ->pluck('total', 'status_laporan');

    $rekapStatus = [];
    foreach (self::STATUS_MAP as $key => $label) {
        $rekapStatus[] = [
            'label' => $label,
            'total' => $rekapRaw[$key] ?? 0
        ];
    }

    $total = array_sum(array_column($rekapStatus, 'total'));

    /* ======================
     * GENERATE PDF
     * ====================== */
    $pdf = app('dompdf.wrapper')->loadView(
        'pdf.rekap-laporan-pertanggal',
        [
            'data'          => $data,
            'rekapTanggal'  => $rekapTanggal,
            'rekapStatus'   => $rekapStatus,
            'total'         => $total,
            'request'       => $request
        ]
    );

    return $pdf->download('rekap-laporan-per-tanggal.pdf');
}
/**
 * @OA\Get(
 *     path="/api/rekap/status/pdf",
 *     tags={"Laporan PDF"},
 *     summary="Download PDF rekap laporan berdasarkan status",
 *     description="Mengunduh laporan rekap jumlah laporan per status dalam format PDF",
 *     security={{"Bearer":{}}},
 *
 *     @OA\Parameter(
 *         name="status_laporan",
 *         in="query",
 *         required=false,
 *         description="Filter status laporan (0=Pengajuan, 1=Diterima, 2=Diverifikasi, 3=Dalam Penanganan, 4=Selesai, 5=Ditolak)",
 *         @OA\Schema(
 *             type="integer",
 *             enum={0,1,2,3,4,5},
 *             example=0
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="PDF berhasil di-generate",
 *         @OA\MediaType(
 *             mediaType="application/pdf",
 *             @OA\Schema(type="string", format="binary")
 *         )
 *     ),
 *
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */

 public function downloadStatus(Request $request)
{
    /* ======================
     * QUERY DASAR
     * ====================== */
    $query = Laporan::query();

    /* ======================
     * FILTER STATUS
     * ====================== */
    if ($request->filled('status_laporan')) {
        $query->where('status_laporan', $request->status_laporan);
    }

    /* ======================
     * DATA LAPORAN
     * ====================== */
    $data = (clone $query)
        ->orderBy('created_at', 'desc')
        ->get();

    /* ======================
     * REKAP STATUS RAW
     * ====================== */
    $rekapRaw = (clone $query)
        ->reorder()
        ->select(
            'status_laporan',
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('status_laporan')
        ->pluck('total', 'status_laporan');

    /* ======================
     * REKAP STATUS MAPPED
     * ====================== */
    $rekapStatus = [];
    foreach (self::STATUS_MAP as $key => $label) {
        $rekapStatus[] = [
            'label' => $label,
            'total' => $rekapRaw[$key] ?? 0
        ];
    }

    $total = array_sum(array_column($rekapStatus, 'total'));

    /* ======================
     * GENERATE PDF
     * ====================== */
    $pdf = app('dompdf.wrapper')->loadView(
        'pdf.rekap-laporan-status',
        [
            'data'        => $data,
            'rekapStatus' => $rekapStatus,
            'total'       => $total,
            'request'     => $request,
            'statusMap'   => self::STATUS_MAP
        ]
    );

    return $pdf->download('rekap-laporan-status.pdf');
}

}
