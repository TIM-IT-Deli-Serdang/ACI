@extends('backend.layout.app')
@section('title', 'Master Data UPT')
@section('content')

    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Master UPT
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a class="text-muted text-hover-primary">Home</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">Master Data</li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-gray-900">List UPT</li>
                </ul>
            </div>
            <div class="d-flex align-items-center pt-4 pb-7 pt-lg-1 pb-lg-2">
                <button type="button" id="btn_tambah_data" class="btn btn-sm btn-primary">
                    <i class="ki-outline ki-plus fs-2"></i> Tambah Data
                </button>
            </div>
        </div>
    </div>
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="card border border-gray-300">
            <div class="card-header border-bottom border-gray-300 bg-secondary">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                        <input type="text" data-kt-user-table-filter="search" id="search"
                            class="form-control w-250px ps-13" placeholder="Cari Nama UPT..." />
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-7 gy-5 chimox" id="chimox">
                    <thead>
                        <tr class="text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2 text-start">No</th>
                            <th class="min-w-150px">Nama UPT</th>
                            <th class="text-end w-150px pe-4">Created At</th>
                            <th class="text-end w-100px pe-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="Modal_Tambah_Data" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content" id="tambah-modal-content">
                <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="fw-bold">Tambah UPT Baru</h4>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                </div>
                <form method="post" id="FormTambahModalID" class="form">
                    @csrf
                    <div class="modal-body px-5 my-2">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-7 mb-2">Nama UPT</label>
                            <input type="text" name="nama_upt" class="form-control mb-3 mb-lg-0"
                                placeholder="Contoh: UPT Wilayah 1" />
                            <span class="text-danger error-text nama_upt_error_add"></span>
                        </div>
                    </div>
                    <div class="modal-footer py-4">
                        <button type="reset" class="btn btn-sm btn-secondary me-3" data-bs-dismiss="modal"
                            onclick="resetForm()">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="btn-add-data">
                            <span class="indicator-label add-data-label">Simpan</span>
                            <span class="indicator-progress add-data-progress" style="display: none;">
                                Please Wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade shadow-sm" id="Modal_Show_Data" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content" id="show-modal-content">
                <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="fw-bold m-0">Detail UPT</h4>
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
    <div class="modal fade" id="Modal_Edit_Data" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content" id="edit-modal-content">
                <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="fw-bold">Edit UPT</h4>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                </div>
                <form id="FormEditModalID" class="form">
                    <div class="modal-body px-5 my-2">
                        @method('PUT')
                        @csrf
                        <div id="EditRowModalBody"></div>
                        </div>
                    <div class="modal-footer py-4">
                        <button type="button" class="btn btn-sm btn-secondary me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="btn-edit-data">
                            <span class="indicator-label edit-data-label">Simpan Perubahan</span>
                            <span class="indicator-progress edit-data-progress" style="display: none;">
                                Please Wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
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
        
        <script>
            function resetForm() {
                $("#FormTambahModalID").trigger('reset');
                $(".error-text").text("");
            }
        </script>

        <script type="text/javascript">
            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            $(document).ready(function() {
                
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
                        url: "{{ route('upt.data') }}", // /master/upt/data
                        type: 'GET',
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'nama_upt', name: 'nama_upt' },
                        { data: 'created_at', name: 'created_at', searchable: false },
                        {
                            data: null, // Menggunakan null karena action digenerate manual di sini (atau ambil dari backend action column)
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                // Ambil ID dari row (bisa id atau _id)
                                var id = row.id ?? row._id ?? '';
                                
                                // Render Dropdown Action
                                return `
                                <div class="dropdown text-end">
                                    <button class="btn btn-sm btn-light btn-active-light-primary dropdown-toggle" type="button" id="dropdownAction_${meta.row}"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        Aksi
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" aria-labelledby="dropdownAction_${meta.row}">
                                        <div class="menu-item px-3">
                                            <a class="menu-link px-3 btn-get-show" data-id="${id}" href="javascript:void(0)">Detail</a>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a class="menu-link px-3 btn-get-edit" data-id="${id}" href="javascript:void(0)">Edit</a>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a class="menu-link px-3 btn-get-delete" data-id="${id}" href="javascript:void(0)">Hapus</a>
                                        </div>
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


                // --- ADD DATA ---
                $('#btn_tambah_data').click(function() {
                    $('#Modal_Tambah_Data').modal('show');
                });

                var targetAdd = document.querySelector("#tambah-modal-content");
                var blockUIAdd = new KTBlockUI(targetAdd, {
                    message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> <span class="text-white">Please Wait...</span></div>',
                    overlayClass: "bg-dark bg-opacity-50",
                });

                $('#FormTambahModalID').on('submit', function(event) {
                    event.preventDefault();
                    blockUIAdd.block();
                    $('#btn-add-data .add-data-label').hide();
                    $('#btn-add-data .add-data-progress').show();
                    $('#btn-add-data').prop('disabled', true);

                    $.ajax({
                        url: "{{ route('upt.store') }}", // /master/upt (POST)
                        method: 'POST',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        beforeSend: function() {
                            $(document).find("span.error-text").text("");
                        },
                        success: function(result) {
                            blockUIAdd.release();
                            $("#Modal_Tambah_Data").modal("hide");
                            $(".chimox").DataTable().ajax.reload();
                            
                            Swal.fire({
                                title: "Berhasil",
                                text: result.message || "Data berhasil disimpan.",
                                icon: "success",
                                timer: 1500,
                                confirmButtonText: "Oke"
                            });

                            $('#btn-add-data .add-data-label').show();
                            $('#btn-add-data .add-data-progress').hide();
                            $('#btn-add-data').prop('disabled', false);
                            resetForm();
                        },
                        error: function(xhr) {
                            blockUIAdd.release();
                            $('#btn-add-data .add-data-label').show();
                            $('#btn-add-data .add-data-progress').hide();
                            $('#btn-add-data').prop('disabled', false);

                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(field, messages) {
                                    $("span." + field + "_error_add").text(messages[0]);
                                });
                            } else {
                                Swal.fire("Error", xhr.responseJSON?.message || "Terjadi kesalahan server.", "error");
                            }
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
                        url: "/master/upt/" + id,
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


                // --- EDIT DATA ---
                var targetEdit = document.querySelector("#edit-modal-content");
                var blockUIEdit = new KTBlockUI(targetEdit, {
                    message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> <span class="text-white">Loading...</span></div>',
                    overlayClass: "bg-dark bg-opacity-50",
                });

                $("body").on("click", ".btn-get-edit", function(e) {
                    e.preventDefault();
                    var id = $(this).data("id");
                    
                    $("#EditRowModalBody").html('<div class="text-center py-10"><div class="spinner-border text-primary"></div><p class="mt-3">Loading...</p></div>');
                    
                    $.ajax({
                        url: "/master/upt/" + id + "/edit",
                        dataType: "json",
                        success: function(result) {
                            if (result.html) {
                                $("#EditRowModalBody").html(result.html);
                                // Inject hidden ID
                                if (!$("#hidden_id").length) {
                                    $("#FormEditModalID").append('<input type="hidden" id="hidden_id" name="id" value="'+id+'"/>');
                                } else {
                                    $("#hidden_id").val(id);
                                }
                                var myModal = new bootstrap.Modal(document.getElementById('Modal_Edit_Data'));
                                myModal.show();
                            } else {
                                $("#EditRowModalBody").html('<p class="text-danger">Gagal load form.</p>');
                            }
                        },
                        error: function() {
                            $("#EditRowModalBody").html('<p class="text-danger">Gagal koneksi server.</p>');
                        }
                    });
                });

                // --- SUBMIT UPDATE ---
                $('#FormEditModalID').on('submit', function(e) {
                    e.preventDefault();
                    blockUIEdit.block();
                    $('#btn-edit-data .edit-data-label').hide();
                    $('#btn-edit-data .edit-data-progress').show();
                    $('#btn-edit-data').prop('disabled', true);

                    var id = $("#hidden_id").val(); 
                    var formData = new FormData(this);
                    formData.append('_method', 'PUT'); // Spoof PUT

                    $.ajax({
                        url: "/master/upt/" + id,
                        method: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(result) {
                            blockUIEdit.release();
                            var modalEl = document.getElementById('Modal_Edit_Data');
                            bootstrap.Modal.getInstance(modalEl).hide();
                            $(".chimox").DataTable().ajax.reload();
                            
                            Swal.fire("Berhasil", "Data berhasil diupdate.", "success");
                            
                            $('#btn-edit-data .edit-data-label').show();
                            $('#btn-edit-data .edit-data-progress').hide();
                            $('#btn-edit-data').prop('disabled', false);
                        },
                        error: function(xhr) {
                            blockUIEdit.release();
                            $('#btn-edit-data .edit-data-label').show();
                            $('#btn-edit-data .edit-data-progress').hide();
                            $('#btn-edit-data').prop('disabled', false);
                            
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(field, messages) {
                                    $("span." + field + "_error_edit").text(messages[0]);
                                });
                            } else {
                                Swal.fire("Error", "Gagal update data.", "error");
                            }
                        }
                    });
                });


                // --- DELETE DATA ---
                $("body").on("click", ".btn-get-delete", function(e) {
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
                                url: "/master/upt/" + id,
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
            var elements = document.querySelectorAll('#Modal_Tambah_Data, #Modal_Edit_Data, #Modal_Show_Data');
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