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
        foreach($rawRoles as $r) {
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
    
    // Cek apakah user punya role admin/petugas
    // array_intersect aman digunakan karena $roles sudah dipastikan string
    $intersect = array_intersect(['Superadmin', 'upt', 'sda'], $roles);
    $isAdminOrPetugas = !empty($intersect); 
@endphp

    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Laporan Warga</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a class="text-muted text-hover-primary">Home</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-gray-900">List Laporan</li>
                </ul>
            </div>
            
            {{-- TOMBOL TAMBAH (KHUSUS MASYARAKAT) --}}
            @if($isMasyarakat)
            <div class="d-flex align-items-center pt-4 pb-7 pt-lg-1 pb-lg-2">
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#Modal_Tambah_Data">
                    <i class="ki-outline ki-plus fs-2"></i> Buat Laporan
                </button>
            </div>
            @endif
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="card border border-gray-300">
            <div class="card-header border-bottom border-gray-300 bg-secondary">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                        <input type="text" data-kt-user-table-filter="search" id="search"
                            class="form-control w-250px ps-13" placeholder="Cari Deskripsi / Alamat" />
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
                            <th class="min-w-150px">Alamat</th>
                            <th class="min-w-100px">Status</th>
                            <th class="text-end w-100px pe-4">Tanggal</th>
                            <th class="text-end w-80px pe-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold"></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH DATA (BARU) --}}
    <div class="modal fade" id="Modal_Tambah_Data" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                    <div class="text-center mb-13">
                        <h1 class="mb-3">Buat Laporan Baru</h1>
                        <div class="text-muted fw-semibold fs-5">Sampaikan keluhan infrastruktur Anda</div>
                    </div>

                    <form id="FormTambahLaporan" class="form" enctype="multipart/form-data">
                        @csrf
                        {{-- Kategori --}}
                        <div class="fv-row mb-8">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Kategori Laporan</span>
                            </label>
                            <select name="kategori_laporan_id" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Pilih Kategori">
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
                            <textarea class="form-control form-control-solid" rows="3" name="deskripsi" placeholder="Jelaskan detail kerusakan..."></textarea>
                        </div>

                        {{-- Alamat --}}
                        <div class="fv-row mb-8">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Alamat Lengkap</span>
                            </label>
                            <textarea class="form-control form-control-solid" rows="2" name="alamat" placeholder="Nama Jalan, Desa, Kecamatan..."></textarea>
                        </div>

                        {{-- Lokasi (Lat/Long) --}}
                        <div class="row mb-8">
                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Latitude</label>
                                <input type="text" class="form-control form-control-solid" name="latitude" placeholder="-3.xxxx" />
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Longitude</label>
                                <input type="text" class="form-control form-control-solid" name="longitude" placeholder="98.xxxx" />
                            </div>
                        </div>

                        {{-- Wilayah --}}
                        <input type="hidden" name="kecamatan_id" value="1"> 
                        <input type="hidden" name="kelurahan_id" value="1">

                        {{-- Foto --}}
                        <div class="fv-row mb-8">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Foto Bukti</label>
                            <input type="file" class="form-control form-control-solid" name="file_masyarakat" accept=".png, .jpg, .jpeg">
                            <div class="text-muted fs-7 mt-2">Format: png, jpg, jpeg. Max 2MB.</div>
                        </div>

                        <div class="text-center">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="btn_simpan_laporan" class="btn btn-primary">
                                <span class="indicator-label">Kirim Laporan</span>
                                <span class="indicator-progress">Mohon tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL SHOW --}}
    <div class="modal fade shadow-sm" id="Modal_Show_Data" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-750px">
            <div class="modal-content" id="show-modal-content">
                <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="fw-bold m-0">Detail Laporan</h4>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                </div>
                <div class="modal-body" id="ShowRowModalBody">
                    <div class="text-center py-10">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-3">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="Modal_Edit_Data" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-550px">
            <div class="modal-content" id="edit-modal-content">
                <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="fw-bold">Edit Laporan</h4>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                </div>
                <form id="FormEditModalID" class="form" enctype="multipart/form-data">
                    <div class="modal-body px-2 my-2">
                        @method('PUT')
                        @csrf
                        <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_edit_scroll"
                            data-kt-scroll="true" data-kt-scroll-max-height="auto" data-kt-scroll-offset="300px">
                            <div id="EditRowModalBody"></div>
                        </div>
                    </div>
                    <div class="modal-footer py-4">
                        <button type="button" class="btn btn-sm btn-secondary me-3" data-bs-dismiss="modal">Batal</button>
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
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { 
                            data: 'kategori_laporan_id', 
                            name: 'kategori_laporan_id',
                            render: function(data) {
                                return kategoriMap[data] ? 
                                    '<span class="badge badge-light-primary fw-bold">'+kategoriMap[data]+'</span>' : '-';
                            }
                        },
                        { data: 'deskripsi', name: 'deskripsi' },
                        { data: 'alamat', name: 'alamat' },
                        { data: 'status_laporan', name: 'status_laporan', orderable: false, searchable: false },
                        { data: 'created_at', name: 'created_at', orderable: false, searchable: false },
                        {
                            data: 'id',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                var urlValidasi = "/laporan/laporan/" + data + "/validation";
                                
                                // LOGIC TOMBOL
                                let btnDetail = `
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 btn-get-show" data-id="${data}">Lihat Detail</a>
                                    </div>`;
                                
                                let btnValidasi = '';
                                if (isAdminOrPetugas) {
                                    btnValidasi = `
                                    <div class="menu-item px-3">
                                        <a href="${urlValidasi}" class="menu-link px-3 text-primary">
                                            <i class="ki-outline ki-shield-tick fs-6 me-2"></i> Validasi
                                        </a>
                                    </div>`;
                                }

                                let btnEdit = '';
                                let btnDelete = '';

                                if (isMasyarakat) {
                                    btnEdit = `
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 btn-get-edit" data-id="${data}">Edit</a>
                                    </div>`;
                                    
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
                });

                $('#search').on('keyup', debounce(function() {
                    table.search($(this).val()).draw();
                }, 500));

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
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(res) {
                            $('#Modal_Tambah_Data').modal('hide');
                            $('#FormTambahLaporan')[0].reset();
                            table.draw();
                            Swal.fire("Berhasil!", "Laporan berhasil dikirim.", "success");
                        },
                        error: function(err) {
                            Swal.fire("Gagal", err.responseJSON.message || "Terjadi kesalahan", "error");
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
                    $("#ShowRowModalBody").html('<div class="text-center py-10"><div class="spinner-border text-primary"></div><p class="mt-3">Loading...</p></div>');
                    var myModal = new bootstrap.Modal(document.getElementById('Modal_Show_Data'));
                    myModal.show();
                    $.ajax({
                        url: "/laporan/laporan/" + id,
                        dataType: "json",
                        success: function(result) {
                            $("#ShowRowModalBody").html(result.html || '<p class="text-danger">Data tidak tersedia.</p>');
                        },
                        error: function() {
                            $("#ShowRowModalBody").html('<p class="text-danger">Gagal memuat data.</p>');
                        }
                    });
                });

                // --- EDIT DATA (Load Form) ---
                $("body").on("click", ".btn-get-edit", function(e) {
                    e.preventDefault();
                    let id = $(this).data("id");
                    
                    $("#EditRowModalBody").html('<div class="text-center py-10"><div class="spinner-border text-primary"></div><p class="mt-3">Loading...</p></div>');
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
                            $("#EditRowModalBody").html('<p class="text-danger">Gagal memuat form edit.</p>');
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
                                data: { _method: 'DELETE', _token: $('meta[name="csrf-token"]').attr('content') },
                                success: function(res) {
                                    table.draw();
                                    Swal.fire("Terhapus!", "Laporan berhasil dihapus.", "success");
                                },
                                error: function() {
                                    Swal.fire("Gagal", "Tidak dapat menghapus data (mungkin sudah diproses).", "error");
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection