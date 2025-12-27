@extends('backend.layout.app')
@section('title', 'List User')
@section('content')


    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        <!--begin::Toolbar wrapper-->
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">User
                    List</h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a class="text-muted text-hover-primary">Home</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->

                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">Resources</li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">User List</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center pt-4 pb-7 pt-lg-1 pb-lg-2">
 <button type="button" id="btn_tambah_data" class="btn btn-sm btn-primary">
                        <i class="ki-outline ki-plus fs-2"></i>Add</button>

            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar wrapper-->
    </div>
    <!--end::Toolbar-->

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Card-->
        <div class="card border border-gray-300">
            <!--begin::Card header-->
            <div class="card-header border-bottom border-gray-300 bg-secondary">
                <!--begin::Card title-->
                <div class="card-title">
                    <!--begin::Search-->
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                        <input type="text" data-kt-user-table-filter="search" id="search"
                            class="form-control  w-250px ps-13" placeholder="Cari user" />
                    </div>
                    <!--end::Search-->
                </div>
                <!--begin::Card title-->
                <!--begin::Card toolbar-->
                <div class="card-toolbar">




                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->


            <div class="card-body py-4">

                <table class="table align-middle table-row-dashed fs-7 gy-5 chimox" id="chimox">
                    <thead>
                        <tr class=" text-muted fw-bold fs-7 text-uppercase gs-0">

                           <th class="min-w-125px">Username</th>
                           <th class="min-w-125px text-center">NIK</th>
                           <th class="min-w-125px">UPT</th>
                            <th class="min-w-100px">Role</th>
                            <th class="min-w-100px">Last login</th>
                            <th class="min-w-100px">Last IP Adress</th>
                            <th class="min-w-100px">Joined Date</th>
                            <th class="text-end w-80px pe-2">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>


            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>



<!--begin::Modal - Add-->
<div class="modal fade" id="Modal_Tambah_Data" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <!--begin::Modal content-->
        <div class="modal-content" id="tambah-modal-content">
            <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="fw-bold">Add Data</h4>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary " data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                    <!--end::Close-->
                </div>
            <form method="post" id="FormTambahModalID" class="form" enctype="multipart/form-data">
                @csrf
                <!--begin::Modal body-->
                <div class="modal-body px-2 my-2">
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add__scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_modal_add_header"
                        data-kt-scroll-wrappers="#kt_modal_add_scroll" data-kt-scroll-offset="300px">

                     

                        <!-- Input group: Nama Brand -->
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-7 mb-2">Nama User</label>
                            <input type="text" name="name" id="name" class="form-control mb-3 mb-lg-0"
                                placeholder="Contoh: Kominfo" />
                            <span class="text-danger error-text name_error_add"></span>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-7 mb-2">NIK</label>
                            <input type="number" name="nik" class="form-control mb-3 mb-lg-0" placeholder="Contoh: 1207xxxx" />
                            <span class="text-danger error-text nik_error_add"></span>
                        </div>
                        
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-7 mb-2">Unit UPT</label>
                            <select id="upt_id_add" name="upt_id" class="form-select mb-3 mb-lg-0"
                                    data-dropdown-parent="#Modal_Tambah_Data" 
                                    data-control="select2" 
                                    data-placeholder="Pilih UPT">
                            </select>
                            <span class="text-danger error-text upt_id_error_add"></span>
                        </div>

                        <div class="row">
                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">Password</label>
                                <input type="text" name="password" id="password" class="form-control mb-3 mb-lg-0"
                                    placeholder="Contoh: Jhon Hunter" />
                                <span class="text-danger error-text password_error_add"></span>
                            </div>

                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">Role</label>
                            <select id="roles" name="roles" class="form-select mb-3 mb-lg-0"
                                    data-dropdown-parent="#Modal_Tambah_Data" data-dropdown-parent="body"
                                    data-control="select2" data-placeholder="Pilih Role"></select>
                            <span class="text-danger error-text role_error_add"></span>

                            </div>
                        </div>

                        <div class="row">
                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">Email</label>
                                <input type="text" name="email" id="email" class="form-control mb-3 mb-lg-0"
                                    placeholder="Contoh: Jhon Hunter" />
                                <span class="text-danger error-text email_error_add"></span>
                            </div>

                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">No. WhatsApp</label>
                                <input type="text" name="no_wa" id="no_wa" class="form-control mb-3 mb-lg-0"
                                    placeholder="Contoh: 1234 xxxx 5678" />
                                <span class="text-danger error-text no_wa_error_add"></span>
                            </div>
                        </div>


                      
                    </div>
                </div>
                <!--end::Modal body-->

                <div class="modal-footer py-4">
                    <button type="reset" class="btn btn-sm btn-secondary me-3" data-bs-dismiss="modal"
                        onclick="resetForm()">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btn-add-data">
                        <span class="indicator-label add-data-label">Simpan</span>
                        <span class="indicator-progress add-data-progress" style="display: none;">
                            Please Wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Add-->


<!-- Begin Modal Show -->
<div class="modal fade " id="Modal_Show_Data" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content" id="show-modal-content">
            <div class="modal-header align-items-center py-6 border-gray-300">
    <h4 class="fw-bold m-0">Detail Data</h4>
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
<!-- End Modal Show -->





    <!-- Begin Modal Edit -->
    <div class="modal fade" id="Modal_Edit_Data" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-550px">
            <div class="modal-content" id="edit-modal-content">
                <div class="modal-header align-items-center py-6 border-gray-300">
                    <h4 class="fw-bold">Edit Data</h4>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                    <!--end::Close-->
                </div>
                <form id="FormEditModalID" class="form" enctype="multipart/form-data">
                <div class="modal-body px-2 my-2">
                    
                        @method('PUT')
                        @csrf
                        <!--begin::Scroll-->
                        <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_edit_user_scroll"
                            data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                            data-kt-scroll-dependencies="#kt_modal_edit_user_header"
                            data-kt-scroll-wrappers="#kt_modal_edit_user_scroll" data-kt-scroll-offset="300px">
                            <div id="EditRowModalBody"></div>
                            <input type="hidden" name="action" id="action" />
                        </div>
                        

                    
                </div>
                <div class="modal-footer py-4">
                            <button type="button" class="btn btn-sm btn-secondary me-3"
                                data-bs-dismiss="modal">Discard</button>
                            <button type="submit" class="btn btn-sm btn-primary" id="btn-edit-data" value="submit">
                                <span class="indicator-label edit-data-label">Submit</span>
                                <span class="indicator-progress edit-data-progress" style="display: none;">Please Wait ...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                        </form>
            </div>
        </div>
    </div>
    <!-- End Modal Edit -->


    @push('stylesheets')
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="stylesheet" href="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
    @endpush

    @push('scripts')
        <script src="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
        
        <script>
            // Fungsi Reset Form
            function resetForm() {
                $("#FormTambahModalID").trigger('reset');
                $(".error-text").text("");
                // Reset Select2 agar kosong kembali
                $('#roles').val(null).trigger('change');
                $('#upt_id_add').val(null).trigger('change');
            }

            // Fungsi Debounce untuk Search
            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            $(document).ready(function() {

                // ================== 1. DATATABLES CONFIGURATION ==================
                var table = $('.chimox').DataTable({
                    processing: true,
                    language: {
                        processing: "Please Wait ...",
                        loadingRecords: false,
                        zeroRecords: "Tidak ada data yang ditemukan",
                        emptyTable: "Tidak ada data yang tersedia di tabel ini",
                        search: "Cari:",
                    },
                    serverSide: true,
                    order: false,
                    ajax: {
                        url: "{{ route('user.data') }}",
                        type: 'GET',
                    },
                    columns: [
                        { data: 'avatar', name: 'avatar', orderable: false, searchable: false },
                        { data: 'nik', name: 'nik' },
                        { data: 'upt.nama_upt', name: 'upt.nama_upt', orderable: false, searchable: false, defaultContent: '-' },
                        { data: 'roles', name: 'roles', orderable: false, searchable: false },
                        { data: 'last_login_at', name: 'last_login_at', orderable: false, searchable: false },
                        { data: 'last_login_ip', name: 'last_login_ip', orderable: false, searchable: false },
                        { data: 'joined_date', name: 'joined_date', orderable: false, searchable: false },
                        {
                            data: null,
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                var id = row.id ?? row._id ?? '';
                                return `
                                <div class="dropdown text-end">
                                    <button class="btn btn-sm btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions 
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-dark">
                                        <li><a class="dropdown-item btn-get-show" data-id="${id}" href="javascript:void(0)">Detail</a></li>
                                        <li><a class="dropdown-item btn-get-edit" data-id="${id}" href="javascript:void(0)">Edit</a></li>
                                        <li><a class="dropdown-item btn-get-delete" data-id="${id}" href="javascript:void(0)">Hapus</a></li>
                                    </ul>
                                </div>`;
                            }
                        }
                    ]
                });

                $('#search').on('keyup', debounce(function() {
                    table.search($(this).val()).draw();
                }, 500));


                // ================== 2. INIT SELECT2 (ROLE & UPT) ==================
                
                // Select2 Role
                $('#roles').select2({
                    dropdownParent: $("#Modal_Tambah_Data"),
                    allowClear: true,
                    placeholder: "Pilih Role",
                    ajax: {
                        url: "{{ route('role.select') }}",
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.name,
                                        id: item.id
                                    }
                                })
                            };
                        }
                    }
                });

                // Select2 UPT (Perbaikan Syntax Disini)
                $('#upt_id_add').select2({
                    dropdownParent: $("#Modal_Tambah_Data"),
                    allowClear: true,
                    width: '100%', // Penting agar tidak mengecil
                    placeholder: "Pilih UPT",
                    ajax: {
                        url: "{{ route('user.select-upt') }}", 
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: data // Controller sudah mengembalikan format {id, text}
                            };
                        }
                    }
                });


                // ================== 3. STORE DATA (TAMBAH) ==================
                $('#btn_tambah_data').click(function() {
                    $('#Modal_Tambah_Data').modal('show');
                });

                var target = document.querySelector("#tambah-modal-content");
                var blockUI = new KTBlockUI(target, {
                    message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> <span class="text-white">Please Wait ...</span></div>',
                    overlayClass: "bg-dark bg-opacity-50",
                });

                $('#FormTambahModalID').on('submit', function(event) {
                    event.preventDefault();
                    blockUI.block();

                    $('#btn-add-data .add-data-label').hide();
                    $('#btn-add-data .add-data-progress').show();
                    $('#btn-add-data').prop('disabled', true);

                    $.ajaxSetup({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                    });

                    $.ajax({
                        url: "{{ route('user.store') }}",
                        method: 'POST',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        dataType: "json",
                        beforeSend: function() {
                            $(document).find("span.error-text").text("");
                        },
                        success: function(result) {
                            $("#Modal_Tambah_Data").modal("hide");
                            $(".chimox").DataTable().ajax.reload();
                            blockUI.release();

                            var msg = result.message || result.success || "Berhasil disimpan.";
                            Swal.fire({
                                title: "Berhasil",
                                text: msg,
                                icon: "success",
                                timer: 1500,
                                confirmButtonText: "Oke",
                            });

                            $('#btn-add-data .add-data-label').show();
                            $('#btn-add-data .add-data-progress').hide();
                            $('#btn-add-data').prop('disabled', false);
                            resetForm(); // Reset form setelah berhasil
                        },
                        error: function(xhr) {
                            blockUI.release();
                            $('#btn-add-data .add-data-label').show();
                            $('#btn-add-data .add-data-progress').hide();
                            $('#btn-add-data').prop('disabled', false);

                            var res = xhr.responseJSON;
                            if (xhr.status === 422 && res && res.errors) {
                                $.each(res.errors, function(field, messages) {
                                    $("span." + field + "_error_add").text(messages[0]);
                                });
                                Swal.fire({ title: "Gagal", text: res.message, icon: "error", timer: 1800 });
                                return;
                            }
                            if (xhr.status === 401) {
                                Swal.fire({ title: "Unauthorized", text: res?.message, icon: "warning" });
                                return;
                            }
                            var message = (res && res.message) || "Terjadi kesalahan pada server.";
                            Swal.fire({ title: "Error", text: message, icon: "error" });
                        }
                    });
                });

                $("#Modal_Tambah_Data").on("hidden.bs.modal", function() {
                    resetForm();
                });


                // ================== 4. SHOW DETAIL ==================
                $("body").on("click", ".btn-get-show", function(e) {
                    e.preventDefault();
                    let id = $(this).data("id");
                    $("#ShowRowModalBody").html(
                        '<div class="text-center py-10"><div class="spinner-border text-primary"></div><p class="mt-3">Loading...</p></div>'
                    );
                    var myModal = new bootstrap.Modal(document.getElementById('Modal_Show_Data'));
                    myModal.show();

                    $.ajax({
                        url: "/user-management/user/" + id,
                        dataType: "json",
                        success: function(result) {
                            if (result.html) {
                                $("#ShowRowModalBody").html(result.html);
                            } else {
                                $("#ShowRowModalBody").html('<p class="text-danger">Data tidak tersedia.</p>');
                            }
                        },
                        error: function(xhr) {
                            $("#ShowRowModalBody").html('<p class="text-danger">Gagal memuat data.</p>');
                        }
                    });
                });


                // ================== 5. EDIT DATA ==================
                var targetedit = document.querySelector("#edit-modal-content");
                var blockUIEdit = new KTBlockUI(targetedit, {
                    message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> <span class="text-white">Please Wait ...</span></div>',
                    overlayClass: "bg-dark bg-opacity-50",
                });

                $("body").on("click", ".btn-get-edit", function(e) {
                    e.preventDefault();
                    var id = $(this).data("id");
                    $("#EditRowModalBody").html('<div class="text-center py-10"><div class="spinner-border text-primary"></div><p class="mt-3">Loading...</p></div>');

                    $.ajax({
                        url: "/user-management/user/" + id + "/edit",
                        dataType: "json",
                        success: function(result) {
                            if (result.html) {
                                $("#EditRowModalBody").html(result.html);
                                if (!$("#hidden_id").length) {
                                    $("#FormEditModalID").append('<input type="hidden" id="hidden_id" name="id" value="'+id+'"/>');
                                } else {
                                    $("#hidden_id").val(id);
                                }
                                var modalEl = document.getElementById('Modal_Edit_Data');
                                var bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                                bsModal.show();
                            } else {
                                $("#EditRowModalBody").html('<p class="text-danger">Gagal memuat form edit.</p>');
                            }
                        },
                        error: function() {
                            $("#EditRowModalBody").html('<p class="text-danger">Gagal memuat data edit.</p>');
                        }
                    });
                });

                $("body").on("submit", "#FormEditModalID", function(e) {
                    e.preventDefault();
                    blockUIEdit.block();
                    $('#btn-edit-data .edit-data-label').hide();
                    $('#btn-edit-data .edit-data-progress').show();
                    $('#btn-edit-data').prop('disabled', true);

                    var form = this;
                    var formData = new FormData(form);
                    var id = $("#hidden_id").val() || formData.get('id');
                    formData.append('_method', 'PUT');

                    $.ajaxSetup({ headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") } });

                    $.ajax({
                        url: "/user-management/user/" + id,
                        method: "POST",
                        data: formData,
                        contentType: false, processData: false, cache: false, dataType: "json",
                        beforeSend: function() { $(document).find("span.error-text").text(""); },
                        success: function(result) {
                            blockUIEdit.release();
                            if (result.errors) {
                                $.each(result.errors, function(field, messages) { $("span." + field + "_error_edit").text(messages[0]); });
                                Swal.fire({ title: "Error", text: result.message, icon: "error" });
                            } else {
                                var modalEl = document.getElementById('Modal_Edit_Data');
                                bootstrap.Modal.getInstance(modalEl)?.hide();
                                $(".chimox").DataTable().ajax.reload();
                                Swal.fire({ text: "Berhasil diupdate.", icon: "success", timer: 1500 });
                            }
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
                                $.each(xhr.responseJSON.errors, function(field, messages) { $("span." + field + "_error_edit").text(messages[0]); });
                                Swal.fire({ title: "Error", text: xhr.responseJSON.message, icon: "error" });
                            } else {
                                Swal.fire({ title: "Error", text: "Terjadi kesalahan server.", icon: "error" });
                            }
                        }
                    });
                });


                // ================== 6. DELETE DATA ==================
                $("body").on("click", ".btn-get-delete", function(e) {
                    e.preventDefault();
                    var id = $(this).data("id");
                    Swal.fire({
                        title: "Yakin ingin menghapus?",
                        text: "Data yang dihapus tidak bisa dikembalikan.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya, hapus",
                        cancelButtonText: "Batal",
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
                            $.ajax({
                                url: "/user-management/user/" + id,
                                method: "POST",
                                data: { _method: "DELETE" },
                                dataType: "json",
                                success: function(res) {
                                    $(".chimox").DataTable().ajax.reload();
                                    Swal.fire({ title: "Terhapus", text: "Data berhasil dihapus.", icon: "success", timer: 1500 });
                                },
                                error: function() {
                                    Swal.fire({ title: "Error", text: "Terjadi kesalahan pada server.", icon: "error" });
                                }
                            });
                        }
                    });
                });

            }); // END DOCUMENT READY
        </script>

        <script>    
            var elements = document.querySelectorAll('.modal');
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