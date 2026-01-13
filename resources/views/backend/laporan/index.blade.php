@extends('backend.layout.app')
@section('title', 'Laporan Warga')
@section('content')

@php
    // Cek Role & Token (Sama seperti sebelumnya)
    $sUser = session('user');
    $roles = collect($sUser['roles'] ?? [])->map(fn($r) => is_string($r) ? $r : ($r['name'] ?? $r->name ?? null))->toArray();

    $isMasyarakat = in_array('masyarakat', $roles);
    $isSuperAdmin = in_array('Superadmin', $roles);
    $isAdminOrPetugas = !empty(array_intersect(['Superadmin', 'upt', 'sda'], $roles));
    
    // Token untuk API External
    $apiToken = session('auth_token'); 
@endphp

    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        {{-- Toolbar Header (Tetap sama) --}}
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Laporan Warga</h1>
            </div>
            @if($isMasyarakat)
            <div class="d-flex align-items-center pt-4 pb-7 pt-lg-1 pb-lg-2">
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalStore">
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
                            <th class="text-end">Tanggal</th>
                            <th class="text-end">Aksi</th>
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

                    {{-- Deskripsi --}}
                    <div class="mb-5">
                        <label class="required form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control form-control-solid" rows="3" required></textarea>
                    </div>

                    {{-- Row Wilayah --}}
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="required form-label">Kecamatan</label>
                            {{-- ID unik untuk Store --}}
                            <select name="wilayah_kecamatan_id" id="kecamatanStore" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modalStore" data-placeholder="Pilih Kecamatan" required>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="required form-label">Desa / Kelurahan</label>
                            {{-- ID unik untuk Store --}}
                            <select name="wilayah_kelurahan_id" id="kelurahanStore" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modalStore" data-placeholder="Pilih Desa/Kelurahan " required>
                                <option value="">-- Pilih Desa --</option>
                            </select>
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="mb-5">
                        <label class="required form-label">Alamat Detail</label>
                        <textarea name="alamat" class="form-control form-control-solid" rows="2" placeholder="Nama Jalan / Dusun" required></textarea>
                    </div>
                    
                    {{-- Lokasi GPS --}}
                    <div class="row mb-5">
                        <div class="col-md-5">
                            <label class="form-label">Latitude</label>
                            <input type="text" name="latitude" id="add_lat" class="form-control form-control-solid" readonly />
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="longitude" id="add_long" class="form-control form-control-solid" readonly />
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" id="btn-get-loc-add" class="btn btn-icon btn-light-primary w-100" title="Ambil Lokasi"><i class="ki-outline ki-geolocation fs-1"></i></button>
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-edit-data">Simpan Perubahan</button>
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
        // Konfigurasi PHP ke JS
        const isMasyarakat = {{ $isMasyarakat ? 'true' : 'false' }};
        const isSuperAdmin = {{ $isSuperAdmin ? 'true' : 'false' }};
        const isAdminOrPetugas = {{ $isAdminOrPetugas ? 'true' : 'false' }};
        
        // Konfigurasi API Kecamatan (Swagger)
        const API_BASE_URL = "https://apiaci-deliserdangsehat.deliserdangkab.go.id";
        const KABUPATEN_ID = 1212; 
        const API_TOKEN = "{{ $apiToken }}"; 

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // --- 1. FUNGSI LOAD KECAMATAN & DESA (Sama seperti sebelumnya) ---
        function loadKecamatan(selector, selectedId = null) {
            let dropdown = $(selector);
            dropdown.html('<option value="">Memuat...</option>');
            $.ajax({
                url: `${API_BASE_URL}/api/wilayah-kecamatan`,
                method: "GET",
                data: { wilayah_kabupaten_id: KABUPATEN_ID, per_page: 100 },
                headers: { "Authorization": "Bearer " + API_TOKEN },
                success: function(res) {
                    let html = '<option value="">-- Pilih Kecamatan --</option>';
                    if (res.data) {
                        res.data.forEach(item => {
                            let nama = item.nama ? item.nama.trim() : "";
                            let isSelected = (selectedId && selectedId == item.id) ? 'selected' : '';
                            html += `<option value="${item.id}" ${isSelected}>${nama}</option>`;
                        });
                    }
                    dropdown.html(html).trigger('change.select2');
                },
                error: function() { dropdown.html('<option value="">Gagal</option>'); }
            });
        }

        function loadKelurahan(kecamatanId, selector, selectedId = null) {
            let dropdown = $(selector);
            if (!kecamatanId) {
                dropdown.html('<option value="">-- Pilih Kecamatan Dulu --</option>').trigger('change.select2');
                return;
            }
            dropdown.html('<option value="">Memuat...</option>');
            $.ajax({
                url: `${API_BASE_URL}/api/wilayah-desa`,
                method: "GET",
                data: { wilayah_kecamatan_id: kecamatanId, per_page: 100 },
                headers: { "Authorization": "Bearer " + API_TOKEN },
                success: function(res) {
                    let html = '<option value="">-- Pilih Desa --</option>';
                    if (res.data) {
                        res.data.forEach(item => {
                            let nama = item.nama ? item.nama.trim() : "";
                            let isSelected = (selectedId && selectedId == item.id) ? 'selected' : '';
                            html += `<option value="${item.id}" ${isSelected}>${nama}</option>`;
                        });
                    }
                    dropdown.html(html).trigger('change.select2');
                },
                error: function() { dropdown.html('<option value="">Gagal</option>'); }
            });
        }

        $(document).ready(function() {
            // --- DATATABLES CONFIG ---
            var table = $('.chimox').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('laporan.data') }}",
                    type: 'GET',
                },
     columns: [
    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
    { 
        data: 'kategori_laporan_id', 
        name: 'kategori_laporan_id',
        render: function(data) {
            const map = {1:'Jalan Rusak', 2:'Drainase', 3:'Banjir', 4:'Jembatan', 5:'Lainnya'};
            return map[data] ?? '-';
        }
    },
    { data: 'deskripsi', name: 'deskripsi' },
    { 
        data: 'alamat', 
        name: 'alamat',
        render: function(data, type, row) {
            let loc = data;
            if(row.nama_kecamatan) loc += `<br><small class="text-muted">Kec. ${row.nama_kecamatan}</small>`;
            return loc;
        }
    },
    { 
        data: 'status_laporan', 
        name: 'status_laporan',
        render: function(data, type, row) {
            let rawVal = (row.status_raw !== undefined && row.status_raw !== null) ? row.status_raw : data;
            let s = parseInt(rawVal);

            if(s == 0) return '<span class="badge badge-light-dark">Pengajuan</span>';
            if(s == 1) return '<span class="badge badge-light-success">Diterima</span>';
            if(s == 2) return '<span class="badge badge-light-primary">Verifikasi</span>';
            if(s == 3) return '<span class="badge badge-light-warning">Penanganan</span>';
            if(s == 4) return '<span class="badge badge-dark">Selesai</span>';
            if(s == 5) return '<span class="badge badge-danger">Ditolak</span>';
            return `<span class="badge badge-light">Status: ${rawVal}</span>`; 
        }
    },
    { data: 'created_at', name: 'created_at' },
    
    // --- [KOLOM AKSI DROPDOWN] ---
    {
        data: 'id',
        name: 'action',
        orderable: false,
        searchable: false,
        render: function(data, type, row) {
            // Ambil Status Angka
            let rawVal = (row.status_raw !== undefined && row.status_raw !== null) ? row.status_raw : row.status_laporan;
            let s = parseInt(rawVal);

            // 1. ITEM: LIHAT DETAIL (Semua User)
            let menuItems = `
                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3 btn-get-show" data-id="${data}">
                        Lihat Detail
                    </a>
                </div>
            `;

            // 2. ITEM: VALIDASI (Khusus Admin/Petugas & Status belum Selesai/Ditolak)
            if (isAdminOrPetugas && s !== 4 && s !== 5) {
                let urlValidasi = "/laporan/laporan/" + data + "/validation";
                menuItems += `
                    <div class="menu-item px-3">
                        <a href="${urlValidasi}" class="menu-link px-3 text-primary">
                            <i class="ki-outline ki-shield-tick fs-5 me-2"></i> Validasi
                        </a>
                    </div>
                `;
            }

            // 3. ITEM: EDIT & HAPUS
            // Muncul jika: Status Pengajuan (0) ATAU User adalah Superadmin
            if (s == 0 || isSuperAdmin) {
                // Edit
                menuItems += `
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3 btn-get-edit" data-id="${data}">
                            Edit
                        </a>
                    </div>
                `;
                
                // Hapus
                menuItems += `
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3 btn-delete text-danger" data-id="${data}">
                            Hapus
                        </a>
                    </div>
                `;
            }

            // RENDER DROPDOWN WRAPPER
            return `
                <div class="dropdown text-end">
                    <button class="btn btn-sm btn-light btn-active-light-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Aksi
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4">
                        ${menuItems}
                    </ul>
                </div>
            `;
        }
    }
]
            });

            $('#search').on('keyup', debounce(function() {
                table.search($(this).val()).draw();
            }, 500));


            // ==========================================
            // LOGIKA KLIK TOMBOL DETAIL (SHOW)
            // ==========================================
            $("body").on("click", ".btn-get-show", function(e) {
                e.preventDefault();
                let id = $(this).data("id");
                
                // 1. Tampilkan Loading di Modal
                $("#ShowRowModalBody").html('<div class="text-center py-10"><div class="spinner-border text-primary"></div><p class="mt-3">Memuat Data...</p></div>');
                
                // 2. Buka Modal
                var myModal = new bootstrap.Modal(document.getElementById('Modal_Show_Data'));
                myModal.show();

                // 3. Panggil Controller show($id)
                $.ajax({
                    url: "/laporan/laporan/" + id, // Pastikan route ini benar
                    type: "GET",
                    dataType: "json",
                    success: function(result) {
                        // Masukkan HTML dari Controller ke Modal Body
                        $("#ShowRowModalBody").html(result.html);
                    },
                    error: function(xhr) {
                        let msg = "Gagal memuat data.";
                        if(xhr.status == 404) msg = "Data tidak ditemukan.";
                        if(xhr.status == 500) msg = "Terjadi kesalahan server.";
                        
                        $("#ShowRowModalBody").html(`<p class="text-danger text-center">${msg}</p>`);
                    }
                });
            });

            // ==========================================
            // LOGIKA TAMBAH (STORE) & EDIT (UPDATE)
            // ==========================================
            // Load Kecamatan saat Modal Tambah Dibuka
            $('#modalStore').on('shown.bs.modal', function () {
                if ($('#kecamatanStore option').length <= 1) loadKecamatan('#kecamatanStore');
            });

            // Dependent Dropdown (Tambah)
            $('#kecamatanStore').on('change', function() {
                loadKelurahan($(this).val(), '#kelurahanStore');
            });

            // Submit Form Tambah
            $('#FormTambahLaporan').on('submit', function(e) {
                e.preventDefault();
                let btn = $('#btn_simpan_laporan');
                let formData = new FormData(this);
                
                btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ route('laporan.store') }}",
                    method: "POST",
                    data: formData,
                    processData: false, contentType: false,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(res) {
                        $('#modalStore').modal('hide');
                        $('#FormTambahLaporan')[0].reset();
                        $('#kecamatanStore').val('').trigger('change');
                        table.draw();
                        Swal.fire("Berhasil", "Laporan terkirim", "success");
                    },
                    error: function(err) {
                        // Error handling detail
                        let msg = err.responseJSON.message || "Gagal menyimpan";
                        if(err.responseJSON.errors) {
                            msg = Object.values(err.responseJSON.errors)[0][0];
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

            // ==========================================
            // LOGIKA HAPUS (DELETE)
            // ==========================================
            $("body").on("click", ".btn-delete", function(e) {
                e.preventDefault();
                var id = $(this).data("id");
                Swal.fire({
                    title: "Yakin hapus?",
                    text: "Data tidak bisa dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/laporan/laporan/" + id,
                            method: 'POST',
                            data: { _method: 'DELETE', _token: $('meta[name="csrf-token"]').attr('content') },
                            success: function(res) {
                                table.draw();
                                Swal.fire("Terhapus!", "Data berhasil dihapus.", "success");
                            },
                            error: function() {
                                Swal.fire("Gagal", "Terjadi kesalahan server.", "error");
                            }
                        });
                    }
                });
            });

            // GPS Button Logic
            $('#btn-get-loc-add').click(function() {
                if(navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(pos => {
                        $('#add_lat').val(pos.coords.latitude);
                        $('#add_long').val(pos.coords.longitude);
                        Swal.fire("Lokasi Ditemukan", "", "success");
                    });
                }
            });
        });
    </script>
@endpush
@endsection