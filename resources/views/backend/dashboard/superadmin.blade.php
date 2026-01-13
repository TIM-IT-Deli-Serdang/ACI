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

        @if(!empty($apiError))
            <div class="alert alert-danger mb-5">
                {{ $apiError }}
            </div>
        @endif

        {{-- ===== STATUS ===== --}}
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
                            // ✅ KEY WAJIB SAMA DENGAN SWAGGER (underscore)
                            $statusOrder = [
                                'pengajuan' => 'Pengajuan',
                                'diterima' => 'Diterima',
                                'diverifikasi' => 'Diverifikasi',
                                'dalam_penanganan' => 'Dalam Penanganan',
                                'selesai' => 'Selesai',
                                'ditolak' => 'Ditolak',
                            ];

                            $statusMeta = [
                                'pengajuan' => ['icon' => 'bi-send', 'bg' => 'bg-primary'],
                                'diterima' => ['icon' => 'bi-check-circle', 'bg' => 'bg-success'],
                                'diverifikasi' => ['icon' => 'bi-shield-check', 'bg' => 'bg-info'],
                                'dalam_penanganan' => ['icon' => 'bi-tools', 'bg' => 'bg-warning'],
                                'selesai' => ['icon' => 'bi-flag', 'bg' => 'bg-dark'],
                                'ditolak' => ['icon' => 'bi-x-circle', 'bg' => 'bg-danger'],
                            ];
                        @endphp

                        @if(empty($rekapStatus))
                            <div class="text-muted">Data status belum tersedia.</div>
                            <div class="text-muted mt-2">Total Laporan: {{ (int)($totalLaporan ?? 0) }}</div>
                        @else
                            <div class="row g-3">
                                @foreach($statusOrder as $key => $label)
                                    @php
                                        $val = (int)($rekapStatus[$key] ?? 0);
                                        $meta = $statusMeta[$key] ?? ['icon' => 'bi-circle', 'bg' => 'bg-secondary'];
                                    @endphp
                                    <div class="col-6 col-md-6 col-lg-4">
                                        <div class="card card-flush border border-gray-300 h-100">
                                            <div class="card-body d-flex align-items-center gap-3">
                                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center {{ $meta['bg'] }}"
                                                     style="width:44px;height:44px;">
                                                    <i class="bi {{ $meta['icon'] }}" style="font-size:18px;color:white;"></i>
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

                            <div class="text-muted mt-4">Total Laporan: <b>{{ (int)($totalLaporan ?? 0) }}</b></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== TABEL DATA TERBARU + CHART TANGGAL ===== --}}
        <div class="row g-5 g-xl-8">

            

            {{-- Chart per tanggal --}}
            <div class="col-xl-12">
                <div class="card card-xl-stretch mb-xl-8 border border-gray-300">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Grafik Laporan per Tanggal</span>
                            <span class="text-muted fw-semibold fs-7">Dari rekap_tanggal (Swagger)</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartTanggal" height="220"></canvas>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

@push('scripts')
<script>
(function(){
    if (typeof Chart === 'undefined') {
        console.error('Chart.js tidak ditemukan. Pastikan plugins.bundle.js Metronic sudah di-load di layout.');
        return;
    }

    const el = document.getElementById('chartTanggal');
    if(!el) return;

    const labels = @json($chartLabels ?? []);
    const values = @json($chartValues ?? []);

    if(!labels || labels.length === 0) return;

    var primaryColor = (typeof KTUtil !== 'undefined' && KTUtil.getCssVariableValue)
        ? (KTUtil.getCssVariableValue('--bs-primary') || '#009ef7')
        : '#009ef7';

    var primaryLightColor = (typeof KTUtil !== 'undefined' && KTUtil.getCssVariableValue)
        ? (KTUtil.getCssVariableValue('--bs-primary-light') || 'rgba(0, 158, 247, 0.2)')
        : 'rgba(0, 158, 247, 0.2)';

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
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } },
            scales: {
                y: { beginAtZero: true },
                x: { }
            }
        }
    });
})();
</script>
@endpush

@endsection
