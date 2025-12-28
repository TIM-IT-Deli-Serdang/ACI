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
            
            {{-- ===== ROW 2: DETAIL STATUS GRID ===== --}}
            <div class="row g-5 g-xl-8 mb-5 mb-xl-10">
                <div class="col-12">
                    <div class="card card-flush border border-gray-300">
                        <div class="card-header pt-5">
                            <div class="card-title">
                                <h3 class="fw-bold m-0">Data Laporan per Status</h3>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                             @php
                                $statusOrder = [
                                    'pengajuan' => 'Pengajuan',
                                    'diterima' => 'Diterima',
                                    'diverifikasi' => 'Diverifikasi',
                                    'dalam penanganan' => 'Dalam Penanganan',
                                    'selesai' => 'Selesai',
                                    'ditolak' => 'Ditolak',
                                ];
                                $statusMeta = [
                                    'pengajuan' => ['icon' => 'bi-send', 'bg' => 'bg-primary'],
                                    'diterima' => ['icon' => 'bi-check-circle', 'bg' => 'bg-success'],
                                    'diverifikasi' => ['icon' => 'bi-shield-check', 'bg' => 'bg-info'],
                                    'dalam penanganan' => ['icon' => 'bi-tools', 'bg' => 'bg-warning'],
                                    'selesai' => ['icon' => 'bi-flag', 'bg' => 'bg-dark'],
                                    'ditolak' => ['icon' => 'bi-x-circle', 'bg' => 'bg-danger'],
                                ];
                            @endphp

                            @if(empty($rekapStatus))
                                <div class="text-muted">Data status belum tersedia.</div>
                            @else
                                <div class="row g-3">
                                    @foreach($statusOrder as $key => $label)
                                        @php
                                            $val = (int)($rekapStatus[$key] ?? 0);
                                            $meta = $statusMeta[$key] ?? ['icon' => 'bi-circle', 'bg' => 'bg-secondary'];
                                        @endphp
                                        <div class="col-6 col-md-4 col-lg-2">
                                            <div class="card card-flush border border-gray-300 h-100">
                                                <div class="card-body d-flex align-items-center gap-3">
                                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center {{ $meta['bg'] }}"
                                                         style="width:44px;height:44px;">
                                                        <i class="bi {{ $meta['icon'] }}" style="font-size:18px;"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="text-muted small">{{ $label }}</div>
                                                        <div class="fw-bold fs-4">{{ $val }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== ROW 2: DETAIL TABEL & CHART ===== --}}
            <div class="row g-5 g-xl-8">
                {{-- Kolom Kiri: Tabel Top Kecamatan --}}
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

                {{-- Kolom Kanan: Chart --}}
                <div class="col-xl-7">
                    <div class="card card-xl-stretch mb-xl-8 border border-gray-300">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Grafik Statistik</span>
                                <span class="text-muted fw-semibold fs-7">Visualisasi Top 10 Kecamatan</span>
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
    {{-- HAPUS CDN CHART.JS --}}
    
    <script>
        (function(){
            // Cek apakah Chart.js sudah terload dari Metronic Plugins
            if (typeof Chart === 'undefined') {
                console.error('Error: Chart.js tidak ditemukan. Pastikan plugins.bundle.js Metronic sudah di-load di layout.');
                return;
            }

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

            // Gunakan Warna dari Variable CSS Metronic (jika tersedia), atau fallback ke hardcode
            var primaryColor = KTUtil.getCssVariableValue('--bs-primary') || '#009ef7';
            var primaryLightColor = KTUtil.getCssVariableValue('--bs-primary-light') || 'rgba(0, 158, 247, 0.2)';

            new Chart(el, {
                type: 'line', 
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Laporan',
                        data: values,
                        borderColor: primaryColor,
                        backgroundColor: primaryLightColor,
                        borderWidth: 2,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: primaryColor,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4
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
                            ticks: { stepSize: 1, color: '#999' },
                            grid: { color: '#f3f3f3' }
                        },
                        x: {
                            ticks: { color: '#999' },
                            grid: { display: false }
                        }
                    }
                }
            });
        })();
    </script>
@endpush

@endsection