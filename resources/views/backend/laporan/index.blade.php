@extends('backend.layout.app')
@section('title', 'Laporan Warga')
@section('content')

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
            
            {{-- <div class="d-flex align-items-center pt-4 pb-7 pt-lg-1 pb-lg-2">
                <button type="button" id="btn_tambah_data" class="btn btn-sm btn-primary">
                    <i class="ki-outline ki-plus fs-2"></i> Buat Laporan
                </button>
            </div> --}}
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

    <div class="modal fade" id="Modal_Tambah_Data" tabindex="-1" aria-hidden="true">
       </div>

    <div class="modal fade shadow-sm" id="Modal_Show_Data" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-750px">
            <div class="modal-content" id="show-modal-content">
                <div class="modal-header align-items-center py-6 border-gray-300" style="cursor: move;">
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

    <div class="modal fade" id="Modal_Edit_Data" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-550px">
            <div class="modal-content" id="edit-modal-content">
                <div class="modal-header align-items-center py-6 border-gray-300" style="cursor: move;">
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
                    language: {
                        processing: "Please Wait ...",
                        zeroRecords: "Tidak ada data yang ditemukan",
                        emptyTable: "Tidak ada data",
                        search: "Cari:",
                    },
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
        // Generate Link URL Validasi
        var urlValidasi = "/laporan/laporan/" + data + "/validation";

        return `
        <div class="dropdown text-end">
            <button class="btn btn-sm btn-light btn-active-light-primary dropdown-toggle" type="button" id="dropdownAction_${meta.row}"
                    data-bs-toggle="dropdown" aria-expanded="false">
                Aksi
            </button>
            <ul class="dropdown-menu dropdown-menu-end menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" aria-labelledby="dropdownAction_${meta.row}">
                
                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3 btn-get-show" data-id="${data}">
                        Lihat Detail
                    </a>
                </div>

                <div class="menu-item px-3">
                    <a href="${urlValidasi}" class="menu-link px-3 text-primary">
                        <i class="ki-outline ki-shield-tick fs-6 me-2"></i> Validasi
                    </a>
                </div>

                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3 btn-get-edit" data-id="${data}">
                        Edit
                    </a>
                </div>

                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3 btn-delete text-danger" data-id="${data}">
                        Hapus
                    </a>
                </div>
            </ul>
        </div>
        `;
    }
}
                    ],
                    // PENTING: Re-init menu Metronic setelah tabel di-render
                    drawCallback: function() {
                        KTMenu.createInstances();
                    }
                });

                $('#search').on('keyup', debounce(function() {
                    table.search($(this).val()).draw();
                }, 500));

                // --- SHOW DATA (Ini yang memperbaiki modal detail) ---
                $("body").on("click", ".btn-get-show", function(e) {
                    e.preventDefault();
                    let id = $(this).data("id");
                    
                    // Reset konten modal
                    $("#ShowRowModalBody").html('<div class="text-center py-10"><div class="spinner-border text-primary"></div><p class="mt-3">Loading...</p></div>');
                    
                    // Tampilkan Modal
                    var myModal = new bootstrap.Modal(document.getElementById('Modal_Show_Data'));
                    myModal.show();

                    // Fetch data
                    $.ajax({
                        url: "/laporan/laporan/" + id,
                        dataType: "json",
                        success: function(result) {
                            if (result.html) {
                                $("#ShowRowModalBody").html(result.html);
                            } else {
                                $("#ShowRowModalBody").html('<p class="text-danger">Data tidak tersedia.</p>');
                            }
                        },
                        error: function() {
                            $("#ShowRowModalBody").html('<p class="text-danger">Gagal memuat data.</p>');
                        }
                    });
                });

                // --- DELETE DATA ---
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
                                    $(".chimox").DataTable().ajax.reload();
                                    Swal.fire("Terhapus!", "Data berhasil dihapus.", "success");
                                },
                                error: function() {
                                    Swal.fire("Gagal", "Terjadi kesalahan server.", "error");
                                }
                            });
                        }
                    });
                });
            });
        </script>

        <script>    
            var elements = document.querySelectorAll('#Modal_Show_Data, #Modal_Edit_Data'); // Tambah modal tidak perlu dimasukkan
            elements.forEach(function(element) {
                dragElement(element);

                function dragElement(elmnt) {
                    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
                    if (elmnt.querySelector('.modal-header')) {
                        elmnt.querySelector('.modal-header').onmousedown = dragMouseDown;
                    } else {
                        elmnt.onmousedown = dragMouseDown;
                    }

                    function dragMouseDown(e) {
                        e = e || window.event;
                        pos3 = e.clientX;
                        pos4 = e.clientY;
                        document.onmouseup = closeDragElement;
                        document.onmousemove = elementDrag;
                    }

                    function elementDrag(e) {
                        e = e || window.event;
                        pos1 = pos3 - e.clientX;
                        pos2 = pos4 - e.clientY;
                        pos3 = e.clientX;
                        pos4 = e.clientY;
                        elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
                        elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
                    }

                    function closeDragElement() {
                        document.onmouseup = null;
                        document.onmousemove = null;
                    }
                }
            });
        </script>
    @endpush
@endsection