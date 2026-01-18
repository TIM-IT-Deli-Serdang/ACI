@extends('backend.layout.app')
@section('title', 'Rekapitulasi Laporan')
@section('content')

    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Rekapitulasi Laporan
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a class="text-muted text-hover-primary">Home</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-gray-900">Rekap Data</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">

        <div class="card border border-gray-300 mb-5">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Filter Data</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Pilih parameter rekapitulasi</span>
                </h3>
            </div>
            <div class="card-body py-3">
                <form id="filterForm">
                    <div class="row mb-5">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Jenis Rekap</label>
                            <select name="filter_type" id="filter_type" class="form-select" data-control="select2"
                                data-hide-search="true">
                                <option value="status">Berdasarkan Status</option>
                                <option value="tanggal">Berdasarkan Tanggal</option>
                                <option value="kecamatan">Berdasarkan Kecamatan</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3 filter-input" id="input_status">
                            <label class="form-label fw-bold">Pilih Status</label>
                            <select name="status_laporan" class="form-select" data-control="select2"
                                data-placeholder="Semua Status">
                                <option value="">Semua Status</option>

                                {{-- PERBAIKAN: Cek auth()->check() dulu untuk mencegah error --}}
                                @if (auth()->check() && !auth()->user()->hasRole('upt'))
                                    <option value="0">Pengajuan</option>
                                @endif

                                <option value="1">Diterima</option>
                                <option value="2">Diverifikasi</option>
                                <option value="3">Dalam Penanganan</option>
                                <option value="4">Selesai</option>
                                <option value="5">Ditolak</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3 filter-input d-none" id="input_tanggal_awal">
                            <label class="form-label fw-bold">Tanggal Awal</label>
                            <input type="date" name="tanggal_awal" class="form-control" value="{{ date('Y-m-01') }}">
                        </div>
                        <div class="col-md-3 mb-3 filter-input d-none" id="input_tanggal_akhir">
                            <label class="form-label fw-bold">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-3 mb-3 filter-input d-none" id="input_kecamatan">
                            <label class="form-label fw-bold">ID Kecamatan</label>
                            <input type="number" name="kecamatan_id" class="form-control" placeholder="Contoh: 120701">
                            <div class="form-text text-muted">Masukkan ID Kecamatan</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" id="btn_apply_filter" class="btn btn-primary">
                            <i class="ki-outline ki-filter fs-2"></i> Tampilkan Data
                        </button>
                        <button type="button" id="btn_export_pdf" class="btn btn-danger">
                            <i class="ki-outline ki-file-pdf fs-2"></i> Export PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-5 g-xl-8 mb-5" id="stats-container">

            {{-- PERBAIKAN: Cek auth()->check() dulu --}}
            @if (auth()->check() && !auth()->user()->hasRole('upt'))
                <div class="col-xl-2 col-4">
                    <div class="card bg-secondary hoverable card-xl-stretch mb-xl-8">
                        <div class="card-body my-3">
                            <span class="d-block fw-bold fs-6 mb-2">Pengajuan</span>
                            <div class="card-title fw-bold text-gray-800 fs-2 mb-0" id="stat_pengajuan">0</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-xl-2 col-4">
                <div class="card bg-info hoverable card-xl-stretch mb-xl-8">
                    <div class="card-body my-3">
                        <span class="d-block text-white fw-bold fs-6 mb-2">Diterima</span>
                        <div class="card-title fw-bold text-white fs-2 mb-0" id="stat_diterima">0</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-4">
                <div class="card bg-primary hoverable card-xl-stretch mb-xl-8">
                    <div class="card-body my-3">
                        <span class="d-block text-white fw-bold fs-6 mb-2">Diverifikasi</span>
                        <div class="card-title fw-bold text-white fs-2 mb-0" id="stat_diverifikasi">0</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-4">
                <div class="card bg-warning hoverable card-xl-stretch mb-xl-8">
                    <div class="card-body my-3">
                        <span class="d-block text-white fw-bold fs-6 mb-2">Penanganan</span>
                        <div class="card-title fw-bold text-white fs-2 mb-0" id="stat_dalam_penanganan">0</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-4">
                <div class="card bg-success hoverable card-xl-stretch mb-xl-8">
                    <div class="card-body my-3">
                        <span class="d-block text-white fw-bold fs-6 mb-2">Selesai</span>
                        <div class="card-title fw-bold text-white fs-2 mb-0" id="stat_selesai">0</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-4">
                <div class="card bg-danger hoverable card-xl-stretch mb-xl-8">
                    <div class="card-body my-3">
                        <span class="d-block text-white fw-bold fs-6 mb-2">Ditolak</span>
                        <div class="card-title fw-bold text-white fs-2 mb-0" id="stat_ditolak">0</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border border-gray-300">
            <div class="card-header border-bottom border-gray-300 bg-light">
                <div class="card-title">
                    <h3 class="fw-bold m-0">Hasil Rekapitulasi</h3>
                </div>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-7 gy-5" id="rekap_table">
                    <thead>
                        <tr class="text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">No</th>
                            <th class="min-w-150px">Kategori</th>
                            <th class="min-w-200px">Deskripsi</th>
                            <th class="min-w-200px">Alamat</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-100px text-end">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold"></tbody>
                </table>
            </div>
        </div>

    </div>

    @push('stylesheets')
        <link rel="stylesheet" href="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
    @endpush

    @push('scripts')
        <script src="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

        <script>
            $(document).ready(function() {
                var table = $('#rekap_table').DataTable({
                    language: {
                        emptyTable: "Silakan klik 'Tampilkan Data' untuk melihat hasil.",
                        zeroRecords: "Data tidak ditemukan"
                    },
                    columns: [{
                            data: null,
                            render: (data, type, row, meta) => meta.row + 1
                        },
                        {
                            data: 'kategori_laporan_id',
                            render: function(val) {
                                const map = {
                                    1: 'Jalan Rusak',
                                    2: 'Drainase',
                                    3: 'Banjir',
                                    4: 'Jembatan',
                                    5: 'Infrastruktur Lain'
                                };
                                return map[val] || 'Lainnya';
                            }
                        },
                        {
                            data: 'deskripsi'
                        },
                        {
                            data: 'alamat'
                        },
                        {
                            data: 'status_laporan',
                            render: function(val) {
                                const badges = {
                                    0: '<span class="badge badge-secondary">Pengajuan</span>',
                                    1: '<span class="badge badge-info">Diterima</span>',
                                    2: '<span class="badge badge-primary">Diverifikasi</span>',
                                    3: '<span class="badge badge-warning">Penanganan</span>',
                                    4: '<span class="badge badge-success">Selesai</span>',
                                    5: '<span class="badge badge-danger">Ditolak</span>'
                                };
                                return badges[val] || '-';
                            }
                        },
                        {
                            data: 'created_at',
                            render: function(val) {
                                return val ? new Date(val).toLocaleDateString('id-ID') : '-';
                            },
                            className: "text-end"
                        }
                    ]
                });

                $('#filter_type').on('change', function() {
                    var val = $(this).val();
                    $('.filter-input').addClass('d-none');

                    if (val === 'status') {
                        $('#input_status').removeClass('d-none');
                    } else if (val === 'tanggal') {
                        $('#input_tanggal_awal, #input_tanggal_akhir, #input_status').removeClass('d-none');
                    } else if (val === 'kecamatan') {
                        $('#input_kecamatan, #input_status').removeClass('d-none');
                    }
                });

                $('#btn_apply_filter').click(function() {
                    var formData = $('#filterForm').serialize();
                    var btn = $(this);

                    btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-2"></span> Loading...');

                    $.ajax({
                        url: "{{ route('rekap.get-data') }}",
                        type: "GET",
                        data: formData,
                        success: function(res) {
                            btn.prop('disabled', false).html(
                                '<i class="ki-outline ki-filter fs-2"></i> Tampilkan Data');

                            if (res.status) {
                                var stats = res.rekap_status;

                                $('#stat_pengajuan').text(stats.pengajuan || 0);
                                $('#stat_diterima').text(stats.diterima || 0);
                                $('#stat_diverifikasi').text(stats.diverifikasi || 0);
                                $('#stat_dalam_penanganan').text(stats.dalam_penanganan || 0);
                                $('#stat_selesai').text(stats.selesai || 0);
                                $('#stat_ditolak').text(stats.ditolak || 0);

                                table.clear().rows.add(res.data).draw();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Data Berhasil Dimuat',
                                    text: 'Total Laporan: ' + res.total,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function() {
                            btn.prop('disabled', false).html(
                                '<i class="ki-outline ki-filter fs-2"></i> Tampilkan Data');
                            Swal.fire('Error', 'Gagal mengambil data rekap.', 'error');
                        }
                    });
                });

                $('#btn_export_pdf').click(function() {
                    if ($('#filter_type').val() === 'kecamatan' && $('input[name="kecamatan_id"]').val() ===
                        '') {
                        Swal.fire('Peringatan', 'Harap isi ID Kecamatan terlebih dahulu.', 'warning');
                        return;
                    }

                    var query = $('#filterForm').serialize();
                    var url = "{{ route('rekap.export') }}?" + query;
                    window.open(url, '_blank');
                });
            });
        </script>
    @endpush
@endsection
