@extends('backend.layout.app')
@section('title', 'Laporan Warga')
@section('content')

    @php
        // Cek Role User dari Session dengan Normalisasi Data
        $sUser = session('user');
        $rawRoles = $sUser['roles'] ?? [];
        $roles = [];

        // Normalisasi: Pastikan roles menjadi array string sederhana ['masyarakat', 'Superadmin']
        if (is_array($rawRoles)) {
            foreach ($rawRoles as $r) {
                if (is_string($r)) {
                    $roles[] = $r;
                } elseif (is_array($r) && isset($r['name'])) {
                    $roles[] = $r['name'];
                } elseif (is_object($r) && isset($r->name)) {
                    $roles[] = $r->name;
                }
            }
        }

        $isMasyarakat = in_array('masyarakat', $roles);

        // Cek Spesifik Superadmin
        $isSuperAdmin = in_array('Superadmin', $roles);

        // Cek Group Admin/Petugas (untuk tombol Validasi)
        $intersect = array_intersect(['Superadmin', 'upt', 'sda'], $roles);
        $isAdminOrPetugas = !empty($intersect);
    @endphp

    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        {{-- Toolbar Header (Tetap sama) --}}
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Laporan Warga</h1>
            </div>

            {{-- TOMBOL TAMBAH (KHUSUS MASYARAKAT) --}}
            @if ($isMasyarakat)
                <div class="d-flex align-items-center pt-4 pb-7 pt-lg-1 pb-lg-2">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#Modal_Tambah_Data">
                        <i class="ki-outline ki-plus fs-2"></i> Buat Laporan
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="card border border-gray-300">
            {{-- Search & Table Header (Tetap sama) --}}
            <div class="card-header border-bottom border-gray-300 bg-secondary">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                        <input type="text" id="search" class="form-control w-250px ps-13" placeholder="Cari Deskripsi / Alamat" />
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-7 gy-5 chimox" id="chimox">
                    <thead>
                        <tr class="text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2 text-start">No</th>
                            <th class="min-w-100px">Kategori</th>
                            <th class="min-w-150px">Deskripsi</th>
                            <th class="min-w-150px">Lokasi</th> {{-- Digabung agar rapi --}}
                            <th class="min-w-100px">Status</th>
                            {{-- Lebarkan kolom tanggal untuk muat progress bar --}}
                            <th class="text-end min-w-150px pe-4">Tanggal & Tracking</th>
                            <th class="text-end w-80px pe-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold"></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- MODAL TAMBAH DATA (Updated dengan Kelurahan) --}}
    {{-- ========================================================== --}}
   <div class="modal fade" id="modalStore" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <form id="FormTambahLaporan" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="fw-bold">Tambah Laporan</h5>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                </div>
                <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                    
                    {{-- Kategori --}}
                    <div class="mb-5 mt-5">
                        <label class="required form-label">Kategori</label>
                        <select name="kategori_laporan_id" class="form-select form-select-solid" required>
                            <option value="">-- Pilih --</option>
                            <option value="1">Jalan Rusak</option>
                            <option value="2">Drainase Tersumbat</option>
                            <option value="3">Banjir</option>
                            <option value="4">Tanggul/Jembatan</option>
                            <option value="5">Infrastruktur Lain</option>
                        </select>
                    </div>

                    <form id="FormTambahLaporan" class="form" enctype="multipart/form-data">
                        @csrf
                        {{-- Kategori --}}
                        <div class="fv-row mb-8">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Kategori Laporan</span>
                            </label>
                            <select name="kategori_laporan_id" class="form-select form-select-solid" data-control="select2"
                                data-hide-search="true" data-placeholder="Pilih Kategori">
                                <option></option>
                                <option value="1">Jalan Rusak</option>
                                <option value="2">Drainase Tersumbat</option>
                                <option value="3">Banjir</option>
                                <option value="4">Tanggul / Jembatan Rusak</option>
                                <option value="5">Infrastruktur Lainnya</option>
                            </select>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="fv-row mb-8">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Deskripsi Masalah</span>
                            </label>
                            <textarea class="form-control form-control-solid" rows="3" name="deskripsi"
                                placeholder="Jelaskan detail kerusakan..."></textarea>
                        </div>

                        {{-- [BARU] Wilayah Deli Serdang --}}
                        <div class="row mb-8">
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Kecamatan</label>
                                {{-- ID Deli Serdang di API ibnuX adalah 1207 --}}
                                <select id="add_kecamatan" name="kecamatan_id" class="form-select form-select-solid"
                                    data-control="select2" data-placeholder="Pilih Kecamatan">
                                    <option></option>
                                </select>
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Kelurahan/Desa</label>
                                <select id="add_kelurahan" name="kelurahan_id" class="form-select form-select-solid"
                                    data-control="select2" data-placeholder="Pilih Kelurahan">
                                    <option></option>
                                </select>
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div class="fv-row mb-8">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Alamat Lengkap</span>
                            </label>
                            <textarea class="form-control form-control-solid" rows="2" name="alamat" placeholder="Nama Jalan, Dusun..."></textarea>
                        </div>

                        {{-- [BARU] Lokasi (Lat/Long) dengan Auto Location --}}
                        <div class="row mb-8">
                            <div class="col-md-5 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Latitude</label>
                                <input type="text" id="add_lat" class="form-control form-control-solid"
                                    name="latitude" placeholder="-3.xxxx" />
                            </div>
                            <div class="col-md-5 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Longitude</label>
                                <input type="text" id="add_long" class="form-control form-control-solid"
                                    name="longitude" placeholder="98.xxxx" />
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" id="btn-get-loc-add" class="btn btn-icon btn-light-primary w-100"
                                    data-bs-toggle="tooltip" title="Ambil Lokasi Saya">
                                    <i class="ki-outline ki-geolocation fs-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Foto --}}
                        <div class="fv-row mb-8">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Foto / Video Bukti</label>
                            <input type="file" class="form-control form-control-solid" name="file_masyarakat"
                                accept=".png, .jpg, .jpeg, .mp4, .mov, .avi, .mkv, .webm">
                            <div class="text-muted fs-7 mt-2">
                                Foto: jpg, jpeg, png (Max 2MB). <br>
                                Video: mp4, mov, avi, mkv, webm (Max 120MB).
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="btn_simpan_laporan" class="btn btn-primary">
                                <span class="indicator-label">Kirim Laporan</span>
                                <span class="indicator-progress">Mohon tunggu... <span
                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>

                    {{-- File Upload --}}
                    <div class="mb-5">
                        <label class="form-label">Foto/Video Bukti</label>
                        <input type="file" name="file_masyarakat" class="form-control form-control-solid" accept="image/*,video/*" />
                        <div class="text-muted fs-7 mt-1">Max: Foto 2MB, Video 120MB</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_simpan_laporan" class="btn btn-primary">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">Loading... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

    {{-- MODAL SHOW (Tetap sama) --}}
    <div class="modal fade" id="Modal_Show_Data" tabindex="-1"><div class="modal-dialog modal-dialog-centered mw-750px"><div class="modal-content"><div class="modal-body" id="ShowRowModalBody"></div></div></div></div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="Modal_Edit_Data" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="fw-bold">Edit Laporan</h4>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i class="ki-outline ki-cross fs-1"></i></div>
                </div>
                <form id="FormEditModalID" enctype="multipart/form-data">
                    <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                        @method('PUT')
                        @csrf
                        <div id="EditRowModalBody"></div> {{-- Form Edit dari Controller di-inject ke sini --}}
                    </div>
                    <div class="modal-footer py-4">
                        <button type="button" class="btn btn-sm btn-secondary me-3"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="btn-edit-data">
                            <span class="indicator-label edit-data-label">Simpan Perubahan</span>
                            <span class="indicator-progress edit-data-progress" style="display: none;">
                                Loading... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('stylesheets')
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="stylesheet" href="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
    @endpush

    @push('scripts')
        <script src="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

        <script type="text/javascript">
            // Pass variable PHP ke JS
            const isMasyarakat = {{ $isMasyarakat ? 'true' : 'false' }};
            // [BARU] Pass variable isSuperAdmin
            const isSuperAdmin = {{ $isSuperAdmin ? 'true' : 'false' }};
            const isAdminOrPetugas = {{ $isAdminOrPetugas ? 'true' : 'false' }};

            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            $(document).ready(function() {
                const kategoriMap = {
                    1: 'Jalan Rusak',
                    2: 'Drainase Tersumbat',
                    3: 'Banjir',
                    4: 'Tanggul/Jembatan',
                    5: 'Infrastruktur Lain'
                };

                // --- DATATABLES CONFIG ---
                var table = $('.chimox').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('laporan.data') }}",
                        type: 'GET',
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'kategori_laporan_id',
                            name: 'kategori_laporan_id',
                            render: function(data) {
                                return kategoriMap[data] ?
                                    '<span class="badge badge-light-primary fw-bold">' + kategoriMap[
                                        data] + '</span>' : '-';
                            }
                        },
                        {
                            data: 'deskripsi',
                            name: 'deskripsi'
                        },
                        {
                            data: 'alamat',
                            name: 'alamat'
                        },

                        // KOLOM STATUS
                        {
                            data: 'status_laporan',
                            name: 'status_laporan',
                            orderable: false,
                            searchable: false
                        },

                        // [MODIFIKASI] KOLOM TANGGAL & TRACKING
                        {
                            data: 'created_at',
                            name: 'created_at',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                // 1. Bersihkan String Tanggal (antisipasi jika ada HTML tag dari backend)
                                let rawDate = data ? data.replace(/<[^>]*>?/gm, '') : '';

                                // 2. Hitung Selisih Waktu
                                let dateObj = new Date(rawDate);
                                let now = new Date();
                                let timeAgo = '-';
                                let isNew = false;

                                if (!isNaN(dateObj.getTime())) {
                                    let diffMs = now - dateObj; // selisih milisecond
                                    let diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
                                    let diffHours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (
                                        1000 * 60 * 60));

                                    timeAgo = `${diffDays} Hari ${diffHours} Jam`;

                                    // Cek jika < 24 jam (86400000 ms)
                                    if (diffMs < 86400000) {
                                        isNew = true;
                                    }
                                }

                                // 3. Logika Badge BARU
                                // Muncul hanya jika Status 0 (Pengajuan) DAN < 24 Jam
                                let status = parseInt(row.status_raw ?? row.status_laporan ??
                                    0); // Ambil status mentah jika ada, atau parsing dari HTML
                                // Tips: controller sebaiknya kirim 'status_raw' (integer) agar lebih akurat. 
                                // Jika tidak, kita coba parsing manual (tapi berisiko jika row.status_laporan berupa HTML badge).
                                // Asumsi: row.status_laporan yang dikirim backend berupa HTML Badge. 
                                // Kita cek 'status_raw' (biasanya ditambahkan di backend pake addColumn('status_raw', $row->status_laporan))
                                // Jika belum ada 'status_raw', kita tebak dari class badge di HTML status_laporan (agak ribet).
                                // LEBIH AMAN: Kita pakai row.status_raw jika Anda menambahkannya di Controller.
                                // Jika tidak, kita gunakan status_raw logic di bawah (fallback ke 0).

                                // *FIX CEPAT*: Di JS ini kita bisa deteksi status dari row.status_laporan stringnya.
                                if (row.status_laporan && typeof row.status_laporan === 'string' && row
                                    .status_laporan.includes('badge-secondary')) status = 0;
                                else if (row.status_laporan && typeof row.status_laporan === 'string' &&
                                    row.status_laporan.includes('badge-info')) status = 1;
                                else if (row.status_laporan && typeof row.status_laporan === 'string' &&
                                    row.status_laporan.includes('badge-primary')) status = 2;
                                else if (row.status_laporan && typeof row.status_laporan === 'string' &&
                                    row.status_laporan.includes('badge-warning')) status = 3;
                                else if (row.status_laporan && typeof row.status_laporan === 'string' &&
                                    row.status_laporan.includes('badge-success')) status = 4;
                                else if (row.status_laporan && typeof row.status_laporan === 'string' &&
                                    row.status_laporan.includes('badge-danger')) status = 5;

                                let badgeNew = '';
                                if (status === 0 && isNew) {
                                    badgeNew =
                                        '<span class="badge badge-light-success border border-success fw-bold ms-2 fs-9 px-2 py-1">BARU</span>';
                                }

                                // 4. Logika Progress Bar
                                let pct = 0;
                                let color = 'secondary';

                                switch (status) {
                                    case 0:
                                        pct = 10;
                                        color = 'secondary';
                                        break; // Pengajuan
                                    case 1:
                                        pct = 25;
                                        color = 'info';
                                        break; // Diterima
                                    case 2:
                                        pct = 50;
                                        color = 'primary';
                                        break; // Verifikasi
                                    case 3:
                                        pct = 75;
                                        color = 'warning';
                                        break; // Penanganan
                                    case 4:
                                        pct = 100;
                                        color = 'success';
                                        break; // Selesai
                                    case 5:
                                        pct = 100;
                                        color = 'danger';
                                        break; // Ditolak
                                }

                                // 5. Render HTML
                                return `
                                    <div class="d-flex flex-column align-items-end w-100">
                                        <div class="d-flex align-items-center mb-1">
                                            ${badgeNew}
                                            <span class="fw-bold text-gray-800 fs-7">${rawDate}</span>
                                        </div>
                                        <div class="text-muted fs-8 fw-semibold mb-2">${timeAgo}</div>
                                        
                                        <div class="d-flex w-100 align-items-center">
                                            <div class="h-6px w-100 bg-light rounded me-2" data-bs-toggle="tooltip" title="Progress: ${pct}%">
                                                <div class="bg-${color} rounded h-6px" role="progressbar" style="width: ${pct}%" aria-valuenow="${pct}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="text-muted fs-9 fw-bold">${pct}%</span>
                                        </div>
                                    </div>
                                `;
                            }
                        },

                        // KOLOM AKSI
                        {
                            data: 'id',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                var urlValidasi = "/laporan/laporan/" + data + "/validation";

                                // Deteksi status lagi untuk aksi
                                var status = 0;
                                if (row.status_laporan && typeof row.status_laporan === 'string' && row
                                    .status_laporan.includes('badge-secondary')) status = 0;
                                else if (row.status_laporan && typeof row.status_laporan === 'string' &&
                                    row.status_laporan.includes('badge-info')) status = 1;
                                else if (row.status_laporan && typeof row.status_laporan === 'string' &&
                                    row.status_laporan.includes('badge-primary')) status = 2;
                                else if (row.status_laporan && typeof row.status_laporan === 'string' &&
                                    row.status_laporan.includes('badge-warning')) status = 3;
                                else if (row.status_laporan && typeof row.status_laporan === 'string' &&
                                    row.status_laporan.includes('badge-success')) status = 4;
                                else if (row.status_laporan && typeof row.status_laporan === 'string' &&
                                    row.status_laporan.includes('badge-danger')) status = 5;

                                // 1. Tombol DETAIL
                                let btnDetail = `
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 btn-get-show" data-id="${data}">Lihat Detail</a>
                                    </div>`;

                                // 2. Tombol VALIDASI
                                let btnValidasi = '';
                                if (isAdminOrPetugas && status !== 4 && status !== 5) {
                                    btnValidasi = `
                                    <div class="menu-item px-3">
                                        <a href="${urlValidasi}" class="menu-link px-3 text-primary">
                                            <i class="ki-outline ki-shield-tick fs-6 me-2"></i> Validasi
                                        </a>
                                    </div>`;
                                }

                                // 3. Tombol EDIT & DELETE
                                // 3. Tombol EDIT
                                let btnEdit = '';
                                // Logika: Hanya Masyarakat dan Status 0
                                if (isMasyarakat && status === 0) {
                                    btnEdit = `
    <div class="menu-item px-3">
        <a href="#" class="menu-link px-3 btn-get-edit" data-id="${data}">Edit</a>
    </div>`;
                                }

                                // 4. Tombol DELETE
                                let btnDelete = '';
                                // Logika: 
                                // - Masyarakat: Status 0
                                // - Superadmin: Status 4 atau 5
                                const canDeleteMasyarakat = (isMasyarakat && status === 0);
                                const canDeleteSuperadmin = (isSuperAdmin && (status === 4 || status ===
                                    5));

                                if (canDeleteMasyarakat || canDeleteSuperadmin) {
                                    btnDelete = `
    <div class="menu-item px-3">
        <a href="#" class="menu-link px-3 btn-delete text-danger" data-id="${data}">Hapus</a>
    </div>`;
                                }

                                return `
<div class="dropdown text-end">
    <button class="btn btn-sm btn-light btn-active-light-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        Aksi
    </button>
    <ul class="dropdown-menu dropdown-menu-end menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4">
        ${btnDetail}
        ${btnValidasi}
        ${btnEdit}
        ${btnDelete}
    </ul>
</div>`;
                            }
                        }
                    ],
                    drawCallback: function() {
                        KTMenu.createInstances();
                    }
                    dropdown.html(html).trigger('change.select2');
                },
                error: function() { dropdown.html('<option value="">Gagal</option>'); }
            });
        }

                // ==========================================
                // LOGIKA WILAYAH (API INTERNAL) - TAMBAH DATA
                // ==========================================

                // 1. Load Kecamatan Deli Serdang saat Modal Dibuka
                $('#Modal_Tambah_Data').on('show.bs.modal', function() {
                    // Cek jika option masih kosong (kecuali placeholder)
                    if ($('#add_kecamatan option').length <= 1) {
                        // Panggil Route Internal
                        $.getJSON("{{ route('ajax.kecamatan') }}", function(res) {
                            $('#add_kecamatan').empty().append(
                                '<option value="">-- Pilih Kecamatan --</option>');

                            // Loop data dari res.data
                            if (res.data && res.data.length > 0) {
                                res.data.forEach(kec => {
                                    $('#add_kecamatan').append(
                                        `<option value="${kec.id}">${kec.nama}</option>`);
                                });
                            }
                        });
                    }
                });

                // 2. Load Kelurahan saat Kecamatan dipilih
                $('#add_kecamatan').on('change', function() {
                    const kecId = $(this).val();

                    $('#add_kelurahan').empty().append('<option value="">Loading...</option>');

                    if (kecId) {
                        // Panggil Route Internal dengan ID Kecamatan
                        var url = "{{ route('ajax.kelurahan', ':id') }}";
                        url = url.replace(':id', kecId);

                        $.getJSON(url, function(res) {
                            $('#add_kelurahan').empty().append(
                                '<option value="">-- Pilih Kelurahan --</option>');

                            if (res.data && res.data.length > 0) {
                                res.data.forEach(kel => {
                                    $('#add_kelurahan').append(
                                        `<option value="${kel.id}">${kel.nama}</option>`);
                                });
                            }
                        });
                    } else {
                        $('#add_kelurahan').empty().append('<option value="">-- Pilih Kelurahan --</option>');
                    }
                });
                // ==========================================
                // LOGIKA AUTO LOCATION (GPS) - TAMBAH DATA
                // ==========================================
                $('#btn-get-loc-add').click(function() {
                    var btn = $(this);
                    if (navigator.geolocation) {
                        btn.addClass(
                            'spinner spinner-primary spinner-center'
                        ); // Efek loading (jika pakai theme metronic)

                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                $('#add_lat').val(position.coords.latitude);
                                $('#add_long').val(position.coords.longitude);
                                btn.removeClass('spinner spinner-primary spinner-center');
                                Swal.fire("Lokasi Ditemukan", "", "success");
                            },
                            function(error) {
                                btn.removeClass('spinner spinner-primary spinner-center');
                                Swal.fire("Gagal", "Pastikan GPS aktif dan izin lokasi diberikan.",
                                    "error");
                            }
                        );
                    } else {
                        Swal.fire("Error", "Browser tidak mendukung Geolocation.", "error");
                    }
                });

                // --- TAMBAH DATA ---
                $('#FormTambahLaporan').on('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                    var btn = $('#btn_simpan_laporan');

                    btn.attr('data-kt-indicator', 'on').prop('disabled', true);

                    $.ajax({
                        url: "{{ route('laporan.store') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            $('#Modal_Tambah_Data').modal('hide');
                            $('#FormTambahLaporan')[0].reset();
                            table.draw();
                            Swal.fire("Berhasil!", "Laporan berhasil dikirim.", "success");
                        },
                        error: function(err) {
                            Swal.fire("Gagal", err.responseJSON.message || "Terjadi kesalahan",
                                "error");
                        },
                        complete: function() {
                            btn.removeAttr('data-kt-indicator').prop('disabled', false);
                        }
                    });
                });

                // --- SHOW DATA ---
                $("body").on("click", ".btn-get-show", function(e) {
                    e.preventDefault();
                    let id = $(this).data("id");
                    $("#ShowRowModalBody").html(
                        '<div class="text-center py-10"><div class="spinner-border text-primary"></div><p class="mt-3">Loading...</p></div>'
                    );
                    var myModal = new bootstrap.Modal(document.getElementById('Modal_Show_Data'));
                    myModal.show();
                    $.ajax({
                        url: "/laporan/laporan/" + id,
                        dataType: "json",
                        success: function(result) {
                            $("#ShowRowModalBody").html(result.html ||
                                '<p class="text-danger">Data tidak tersedia.</p>');
                        },
                        error: function() {
                            $("#ShowRowModalBody").html(
                                '<p class="text-danger">Gagal memuat data.</p>');
                        }
                        Swal.fire("Gagal", msg, "error");
                    },
                    complete: function() {
                        btn.prop('disabled', false).text('Simpan');
                    }
                });
            });

            $("body").on("click", ".btn-get-edit", function(e) {
                    e.preventDefault();
                    let id = $(this).data("id");

                    $("#EditRowModalBody").html(
                        '<div class="text-center py-10"><div class="spinner-border text-primary"></div><p class="mt-3">Loading...</p></div>'
                    );
                    var myModal = new bootstrap.Modal(document.getElementById('Modal_Edit_Data'));
                    myModal.show();

                    $.ajax({
                        url: "/laporan/laporan/" + id + "/edit",
                        dataType: "json",
                        success: function(result) {
                            $("#EditRowModalBody").html(result.html);
                            // Simpan ID untuk submit
                            $('#FormEditModalID').data('id', id);
                        },
                        error: function() {
                            $("#EditRowModalBody").html(
                                '<p class="text-danger">Gagal memuat form edit.</p>');
                        }
                    });
                });

                // --- UPDATE DATA (Submit) ---
                $('#FormEditModalID').on('submit', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    var formData = new FormData(this);
                    var btn = $('#btn-edit-data');

                    formData.append('_method', 'PUT');

                    btn.find('.indicator-label').hide();
                    btn.find('.indicator-progress').show();
                    btn.prop('disabled', true);

                    $.ajax({
                        url: "/laporan/laporan/" + id,
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            $('#Modal_Edit_Data').modal('hide');
                            table.draw();
                            Swal.fire("Berhasil!", "Data berhasil diperbarui.", "success");
                        },
                        error: function(err) {
                            Swal.fire("Gagal", err.responseJSON.message || "Gagal update", "error");
                        },
                        complete: function() {
                            btn.find('.indicator-label').show();
                            btn.find('.indicator-progress').hide();
                            btn.prop('disabled', false);
                        }
                    });
                });

                // --- DELETE DATA ---
                $("body").on("click", ".btn-delete", function(e) {
                    e.preventDefault();
                    var id = $(this).data("id");
                    Swal.fire({
                        title: "Yakin hapus?",
                        text: "Laporan yang dihapus tidak bisa dikembalikan!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya, Hapus!",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "/laporan/laporan/" + id,
                                method: 'POST',
                                data: {
                                    _method: 'DELETE',
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(res) {
                                    table.draw();
                                    Swal.fire("Terhapus!", "Laporan berhasil dihapus.",
                                        "success");
                                },
                                error: function() {
                                    Swal.fire("Gagal",
                                        "Tidak dapat menghapus data (mungkin sudah diproses).",
                                        "error");
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
