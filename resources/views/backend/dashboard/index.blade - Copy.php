@extends('backend.layout.app')
@section('title', 'Dashboard')
@section('content')

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Dashboard Overview
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">Home</li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">Dashboard</li>
                </ul>
            </div>
        </div>
    </div>
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <div class="row g-5 g-xl-8 mb-5 mb-xl-10">
                <div class="col-md-4 col-xl-3">
                    <div class="card bg-primary hoverable card-xl-stretch mb-xl-8">
                        <div class="card-body d-flex align-items-center pt-3 pb-0">
                            <i class="ki-outline ki-abstract-26 text-white fs-3x ms-n1 me-3"></i>
                            <div class="d-flex flex-column h-100">
                                <span class="text-white fw-bold fs-2 mb-0 mt-2">{{ $totalLaporan ?? 0 }}</span>
                                <span class="text-white opacity-75 fw-semibold fs-6">Total Laporan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-xl-3">
                    <div class="card bg-secondary hoverable card-xl-stretch mb-xl-8">
                        <div class="card-body d-flex align-items-center pt-3 pb-0">
                            <i class="ki-outline ki-file-added text-dark fs-3x ms-n1 me-3"></i>
                            <div class="d-flex flex-column h-100">
                                <span class="text-dark fw-bold fs-2 mb-0 mt-2">{{ $rekapStatus['pengajuan'] ?? 0 }}</span>
                                <span class="text-dark opacity-75 fw-semibold fs-6">Pengajuan Baru</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-xl-3">
                    <div class="card bg-info hoverable card-xl-stretch mb-xl-8">
                        <div class="card-body d-flex align-items-center pt-3 pb-0">
                            <i class="ki-outline ki-check-square text-white fs-3x ms-n1 me-3"></i>
                            <div class="d-flex flex-column h-100">
                                <span class="text-white fw-bold fs-2 mb-0 mt-2">{{ $rekapStatus['diterima'] ?? 0 }}</span>
                                <span class="text-white opacity-75 fw-semibold fs-6">Diterima Admin</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-xl-3">
                    <div class="card bg-dark hoverable card-xl-stretch mb-xl-8">
                        <div class="card-body d-flex align-items-center pt-3 pb-0">
                            <i class="ki-outline ki-shield-tick text-white fs-3x ms-n1 me-3"></i>
                            <div class="d-flex flex-column h-100">
                                <span class="text-white fw-bold fs-2 mb-0 mt-2">{{ $rekapStatus['diverifikasi'] ?? 0 }}</span>
                                <span class="text-white opacity-75 fw-semibold fs-6">Telah Diverifikasi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-xl-3">
                    <div class="card bg-warning hoverable card-xl-stretch mb-xl-8">
                        <div class="card-body d-flex align-items-center pt-3 pb-0">
                            <i class="ki-outline ki-wrench text-white fs-3x ms-n1 me-3"></i>
                            <div class="d-flex flex-column h-100">
                                <span class="text-white fw-bold fs-2 mb-0 mt-2">{{ $rekapStatus['dalam_penanganan'] ?? 0 }}</span>
                                <span class="text-white opacity-75 fw-semibold fs-6">Sedang Ditangani</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-xl-3">
                    <div class="card bg-success hoverable card-xl-stretch mb-xl-8">
                        <div class="card-body d-flex align-items-center pt-3 pb-0">
                            <i class="ki-outline ki-like text-white fs-3x ms-n1 me-3"></i>
                            <div class="d-flex flex-column h-100">
                                <span class="text-white fw-bold fs-2 mb-0 mt-2">{{ $rekapStatus['selesai'] ?? 0 }}</span>
                                <span class="text-white opacity-75 fw-semibold fs-6">Selesai Dikerjakan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-xl-3">
                    <div class="card bg-danger hoverable card-xl-stretch mb-xl-8">
                        <div class="card-body d-flex align-items-center pt-3 pb-0">
                            <i class="ki-outline ki-cross-circle text-white fs-3x ms-n1 me-3"></i>
                            <div class="d-flex flex-column h-100">
                                <span class="text-white fw-bold fs-2 mb-0 mt-2">{{ $rekapStatus['ditolak'] ?? 0 }}</span>
                                <span class="text-white opacity-75 fw-semibold fs-6">Laporan Ditolak</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-5 g-xl-8">
                <div class="col-xl-5">
                    <div class="card card-xl-stretch mb-xl-8 border border-gray-300">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Top Kecamatan</span>
                                <span class="text-muted fw-semibold fs-7">Jumlah laporan tertinggi</span>
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                    <thead>
                                        <tr class="fw-bold text-muted">
                                            <th class="min-w-150px">Kecamatan</th>
                                            <th class="min-w-100px text-end">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($kecamatanCounts))
                                            @foreach(array_slice($kecamatanCounts, 0, 10) as $row)
                                                <tr>
                                                    <td>
                                                        {{-- FIX: Gunakan strip_tags untuk membersihkan HTML badge yang ikut dari API --}}
                                                        <span class="text-dark fw-bold text-hover-primary d-block fs-6">
                                                            {{ strip_tags($row['nama']) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="badge badge-light-primary fs-7 fw-bold">{{ (int)$row['total'] }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr><td colspan="2" class="text-center text-muted">Data tidak tersedia</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-7">
                    <div class="card card-xl-stretch mb-xl-8 border border-gray-300">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Grafik Kecamatan (Top 10)</span>
                                <span class="text-muted fw-semibold fs-7">Visualisasi data laporan</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="chartKecamatan" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function(){
            const el = document.getElementById('chartKecamatan');
            if(!el) return;

            // Bersihkan label dari tag HTML jika ada
            const rawLabels = @json($chartLabels ?? []);
            const labels = rawLabels.map(label => {
                var tempDiv = document.createElement("div");
                tempDiv.innerHTML = label;
                return tempDiv.textContent || tempDiv.innerText || "";
            });

            const values = @json($chartValues ?? []);

            if(labels.length === 0) return;

            new Chart(el, {
                type: 'line', // UBAH KE LINE
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Laporan',
                        data: values,
                        borderColor: '#009ef7', // Warna garis (Primary Metronic)
                        backgroundColor: 'rgba(0, 158, 247, 0.2)', // Warna area bawah garis
                        borderWidth: 2,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#009ef7',
                        pointRadius: 4,
                        fill: true, // Isi area di bawah garis
                        tension: 0.4 // Membuat garis melengkung halus (smooth)
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        })();
    </script>
@endpush

@endsection