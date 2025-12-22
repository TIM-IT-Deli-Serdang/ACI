@extends('backend.layout.app')
@section('title', 'List Role')
@section('content')


    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        <!--begin::Toolbar wrapper-->
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Role
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
                    <li class="breadcrumb-item text-gray-900">Role List</li>
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


    @php $perms = session('user')['permissions'] ?? []; @endphp

@if(in_array('user.list', $perms))
    <!-- menu user management (should display) -->
    <div>DEBUG: menu tampil (manual check)</div>
@else
    <div>DEBUG: menu tidak tampil (manual check)</div>
@endif


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
                        <input type="text" data-kt-role-table-filter="search" id="search"
                            class="form-control  w-250px ps-13" placeholder="Cari role" />
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

                           <th class="min-w-125px">HAK AKSES</th>
                            <th class="min-w-125px">GUARD NAME</th>
                            <th class="min-w-125px">Update At</th>
                            <th class="min-w-125px">Created At</th>
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
    <div class="modal-dialog modal-dialog-centered mw-950px">
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
                    <!--begin::Scroll-->
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" 
                        id="kt_modal_add_role_scroll"
                        data-kt-scroll="true" 
                        data-kt-scroll-activate="true"
                        data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_modal_add_role_header"
                        data-kt-scroll-wrappers="#kt_modal_add_role_scroll" 
                        data-kt-scroll-offset="300px">

                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold mb-2">Role Name</label>
                            <input type="text" name="name" id="name" 
                                class="form-control form-control-solid mb-3 mb-lg-0" 
                                placeholder="Role Name" />
                            <span class="text-danger error-text name_error_add"></span>
                        </div>

                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">List Permissions</label>
                            <span class="text-danger error-text permissions_error_add"></span>

                            <!--begin::Wrapper for permissions-->
                            <div id="permissions-wrapper" class="row g-9 mb-8">
                                <div id="permissions-loading" class="text-center py-5">
                                    <span class="spinner-border spinner-border-sm text-primary me-2"></span>
                                    Memuat permissions...
                                </div>
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Scroll-->
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
    <div class="modal-dialog modal-dialog-centered mw-950px">
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
        <div class="modal-dialog modal-dialog-centered mw-950px">
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
                        <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_edit_role_scroll"
                            data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                            data-kt-scroll-dependencies="#kt_modal_edit_role_header"
                            data-kt-scroll-wrappers="#kt_modal_edit_role_scroll" data-kt-scroll-offset="300px">
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
document.addEventListener("DOMContentLoaded", function () {

    const wrapper = document.getElementById("permissions-wrapper");
    const loading = document.getElementById("permissions-loading");

    fetch("{{ route('role.permissions') }}?cache_minutes=10", {
        headers: {
            "Accept": "application/json"
        },
        credentials: "same-origin"
    })
    .then(res => res.json())
    .then(json => {
        loading.remove();

        if (!json || !json.data) {
            wrapper.innerHTML = "<div class='text-danger'>Gagal memuat permissions.</div>";
            return;
        }

        const data = json.data; 

        Object.keys(data).forEach(category => {

            let col = document.createElement("div");
            col.classList.add("col-md-4", "fv-row");

            let html = `
                <label class="fs-6 fw-semibold mb-2">${category}</label>
                <div>
            `;

            data[category].forEach(item => {
                html += `
                    <label class="form-check form-check-sm form-check-custom mb-2 me-5 me-lg-2">
                        <input class="form-check-input" type="checkbox" 
                               name="permissions[]" 
                               id="perm-${item.id}"
                               value="${item.id}">
                        <span class="form-check-label">${item.name}</span>
                    </label>
                `;
            });

            html += `</div>`;
            col.innerHTML = html;
            wrapper.appendChild(col);
        });

    })
    .catch(err => {
        loading.innerHTML = "<div class='text-danger'>Error memuat permissions.</div>";
        console.error(err);
    });
});
</script>
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
                        url: "{{ route('role.data') }}",
                        type: 'GET',

                    },
                    columns: [

                      {
                            data: "name",
                            name: "name",
                            orderable: false
                        },
                        {
                            data: "guard_name",
                            name: "guard_name",
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: "updated_at",
                            name: "updated_at",
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: "created_at",
                            name: "created_at",
                            orderable: false,
                            searchable: false
                        },
                     
                        {
                        data: null,
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            var id = row.id ?? row._id ?? '';
                            // gunakan class bukan id untuk tombol agar aman meskipun banyak baris
                            return `
                            <div class="dropdown text-end">
                                <button class="btn btn-sm btn-dark dropdown-toggle" type="button" id="dropdownAction_${meta.row}"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                Actions 
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownAction_${meta.row}">
                                <li><a class="dropdown-item btn-get-show" data-id="${id}" href="javascript:void(0)">Detail</a></li>
                                <li><a class="dropdown-item btn-get-edit" data-id="${id}" href="javascript:void(0)">Edit</a></li>
                                <li><a class="dropdown-item btn-get-delete" data-id="${id}" href="javascript:void(0)">Hapus</a></li>

                                </ul>
                            </div>
                            `;
                        }
                        }


                    ]
                });

                $('#search').on('keyup', debounce(function() {
                    var table = $('.chimox').DataTable();
                    table.search($(this).val()).draw();
                }, 500));



               ////////////////////////BEGIN STORE DATA///////////////////////

// SHOW MODAL TAMBAH DATA
$('#btn_tambah_data').click(function() {
    $('#Modal_Tambah_Data').modal('show');
});

var target = document.querySelector("#tambah-modal-content");
var blockUI = new KTBlockUI(target, {
    message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> <span class="text-white">Please Wait ...</span></div>',
    overlayClass: "bg-dark bg-opacity-50",
});

$('#FormTambahModalID').off('submit').on('submit', function(event) {
    event.preventDefault();
    blockUI.block();

    $('#btn-add-data .add-data-label').hide();
    $('#btn-add-data .add-data-progress').show();
    $('#btn-add-data').prop('disabled', true);

    // build FormData dari form
    var fd = new FormData(this);

    // pastikan guard_name (mirip swagger) dikirim
    if (!fd.get('guard_name')) {
        fd.append('guard_name', 'web');
    }

    // pastikan permissions[] konsisten
    fd.delete('permissions[]');
    $('input[name="permissions[]"]:checked').each(function() {
        fd.append('permissions[]', $(this).val());
    });

    // debug isi FormData (optional)
    for (let pair of fd.entries()) {
        console.log('formdata', pair[0], pair[1]);
    }

    // prepare headers: CSRF + Accept + optional Bearer token
    var headers = {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept': 'application/json'
    };
    var token = localStorage.getItem('api_token'); // ubah jika token disimpan lain
    if (token) headers['Authorization'] = 'Bearer ' + token;

    $.ajax({
        url: "{{ route('role.store') }}",
        method: 'POST',
        data: fd,
        contentType: false,
        processData: false,
        cache: false,
        dataType: "json",
        headers: headers,
        beforeSend: function() {
            $(document).find("span.error-text").text("");
        },
        success: function(result) {
            console.log('success', result);
            $("#Modal_Tambah_Data").modal("hide");
            $(".chimox").DataTable().ajax.reload();
            blockUI.release();

            var msg = result.message || result.success || "Berhasil disimpan.";
            Swal.fire({ title: "Berhasil", text: msg, icon: "success", timer: 1500 });

            $('#btn-add-data .add-data-label').show();
            $('#btn-add-data .add-data-progress').hide();
            $('#btn-add-data').prop('disabled', false);
        },
        error: function(xhr, textStatus, errorThrown) {
            blockUI.release();
            $('#btn-add-data .add-data-label').show();
            $('#btn-add-data .add-data-progress').hide();
            $('#btn-add-data').prop('disabled', false);

            // log lengkap untuk debugging
            console.error('XHR status:', xhr.status);
            console.error('Response headers:\n', xhr.getAllResponseHeaders());
            console.error('Response text:\n', xhr.responseText);

            var res = {};
            try { res = xhr.responseJSON || JSON.parse(xhr.responseText || '{}'); } catch(e) { res = {}; }

            // tampilkan validation errors jika ada
            if (xhr.status === 422 && res.errors) {
                $.each(res.errors, function(field, messages) {
                    var selector = "span." + field + "_error_add";
                    $(selector).text(messages[0]);
                });
                Swal.fire({ title: "Gagal", text: res.message || "Validasi gagal", icon: "error" });
                return;
            }

            if (xhr.status === 401) {
                Swal.fire({ title: "Unauthorized", text: res.message || "Sesi habis", icon: "warning" });
                return;
            }

            // tampilkan server message / errorMessage bila ada
            var serverMsg = res.errorMessage || res.message || null;
            Swal.fire({ title: "Error", text: serverMsg || "Terjadi kesalahan pada server.", icon: "error" });
        }
    });
});


// Tombol "Batal"
$("#Modal_Tambah_Data").on("hidden.bs.modal", function() {
    resetForm();
});
////////////////////////END STORE DATA///////////////////////


                ////////////////////////BEGIN SHOW MODAL DETAIL///////////////////////
                
                $("body").on("click", ".btn-get-show", function(e) {
                    e.preventDefault();

                    let id = $(this).data("id");
                    // tampilkan loading sementara
                    $("#ShowRowModalBody").html(
                        '<div class="text-center py-10"><div class="spinner-border text-primary"></div><p class="mt-3">Loading...</p></div>'
                    );
                    // show modal (Bootstrap 5)
                    var myModal = new bootstrap.Modal(document.getElementById('Modal_Show_Data'));
                    myModal.show();

                    $.ajax({
                        url: "/user-management/role/" + id,
                        dataType: "json",
                        success: function(result) {
                            if (result.html) {
                                $("#ShowRowModalBody").html(result.html);
                            } else {
                                $("#ShowRowModalBody").html('<p class="text-danger">Data tidak tersedia.</p>');
                            }
                        },
                        error: function(xhr) {
                            let msg = 'Gagal memuat data.';
                            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                            $("#ShowRowModalBody").html('<p class="text-danger">' + msg + '</p>');
                        }
                    });
                });


                ////////////////////////BEGIN SHOW MODAL DETAIL///////////////////////


                //////////////////////////BEGIN UPDATE DATA///////////////////////
                var targetedit = document.querySelector("#edit-modal-content");
                var blockUIEdit = new KTBlockUI(targetedit, {
                    message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> <span class="text-white">Please Wait ...</span></div>',
                    overlayClass: "bg-dark bg-opacity-50",
                });

                // EDIT MODAL

                var id;
                // ======= OPEN EDIT MODAL (event delegation for class .btn-get-edit) =======
                $("body").on("click", ".btn-get-edit", function(e) {
                    e.preventDefault();

                    // ambil id dari tombol
                    var id = $(this).data("id");

                    // show loader di modal body
                    $("#EditRowModalBody").html('<div class="text-center py-10"><div class="spinner-border text-primary"></div><p class="mt-3">Loading...</p></div>');

                    // ambil partial edit dari server
                    $.ajax({
                        url: "/user-management/role/" + id + "/edit",   // sesuaikan route
                        dataType: "json",
                        success: function(result) {
                            if (result.html) {
                                $("#EditRowModalBody").html(result.html);

                                // pastikan partial menyertakan <input id="hidden_id" name="id" value="..."> atau kita tambahkan
                                if (!$("#hidden_id").length) {
                                    // tambahkan hidden id supaya mudah diambil saat submit
                                    $("#FormEditModalID").append('<input type="hidden" id="hidden_id" name="id" value="'+id+'"/>');
                                } else {
                                    $("#hidden_id").val(id);
                                }

                                // tampilkan modal menggunakan Bootstrap 5 API (lebih aman)
                                var modalEl = document.getElementById('Modal_Edit_Data');
                                if (modalEl) {
                                    var bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                                    bsModal.show();
                                } else {
                                    console.error('Modal element not found: #Modal_Edit_Data');
                                }
                            } else {
                                $("#EditRowModalBody").html('<p class="text-danger">Gagal memuat form edit.</p>');
                            }
                        },
                        error: function(xhr) {
                            var msg = 'Gagal memuat data edit.';
                            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                            $("#EditRowModalBody").html('<p class="text-danger">' + msg + '</p>');
                        }
                    });
                });


                // ======= SUBMIT UPDATE VIA AJAX =======
                // ======= SUBMIT UPDATE VIA AJAX (perbaikan: pastikan permissions[] dikirim) =======
$("body").off("submit", "#FormEditModalID").on("submit", "#FormEditModalID", function(e) {
    e.preventDefault();

    // clear previous errors
    $(document).find("span.error-text").text("");

    // ambil id dari hidden input (pastikan blade sudah pakai name="id" pada #hidden_id)
    var id = $("#hidden_id").val();
    if (!id) {
        // fallback: cek form data
        var tempFd = new FormData(this);
        id = tempFd.get('id') || tempFd.get('hidden_id');
    }

    // ambil semua checkbox permissions yang tercentang
    var checkedBoxes = $(this).find('input[name="permissions[]"]:checked');

    // optional: tampilkan error client-side kalau tidak ada permission yang dicentang
    if (checkedBoxes.length === 0) {
        $("span.permissions_error_edit").text("Permission wajib diisi.");
        // scroll ke area permission supaya user lihat pesan
        var el = document.getElementById('permissions-wrapper-edit');
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    // block UI
    blockUIEdit.block();
    $('#btn-edit-data .edit-data-label').hide();
    $('#btn-edit-data .edit-data-progress').show();
    $('#btn-edit-data').prop('disabled', true);

    // buat FormData baru — kita bangun manual untuk menghindari input yang tidak relevan
    var formData = new FormData();

    // tambahkan nama role
    var nameVal = $(this).find('input[name="name"]').val();
    formData.append('name', nameVal);

    // tambahkan permission[] yang tercentang
    checkedBoxes.each(function() {
        formData.append('permissions[]', $(this).val());
    });

    // method override untuk PUT
    formData.append('_method', 'PUT');

    // debug: lihat isi FormData (bisa dihapus nanti)
    // for (var pair of formData.entries()) { console.log(pair[0]+ ': ' + pair[1]); }

    $.ajaxSetup({
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") }
    });

    $.ajax({
        url: "/user-management/role/" + id,
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        cache: false,
        dataType: "json",
        beforeSend: function() {
            // clear any previous errors
            $(document).find("span.error-text").text("");
        },
        success: function(result) {
            blockUIEdit.release();
            $('#btn-edit-data .edit-data-label').show();
            $('#btn-edit-data .edit-data-progress').hide();
            $('#btn-edit-data').prop('disabled', false);

            if (result.errors) {
                $.each(result.errors, function(field, messages) {
                    $("span." + field + "_error_edit").text(messages[0]);
                });
                Swal.fire({ title: "Error", text: result.message || "Validasi gagal.", icon: "error", timer: 1500 });
                return;
            }

            if (result.error || result.status === false) {
                $("#Modal_Edit_Data").modal("hide");
                Swal.fire({ title: result.judul || "Error", text: result.error || result.message || "Terjadi kesalahan.", icon: "error", timer: 1500 });
                return;
            }

            // sukses
            var modalEl = document.getElementById('Modal_Edit_Data');
            if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();
            $(".chimox").DataTable().ajax.reload();
            Swal.fire({ text: result.success || "Berhasil diupdate.", icon: "success", timer: 1500 });
        },
        error: function(xhr) {
            blockUIEdit.release();
            $('#btn-edit-data .edit-data-label').show();
            $('#btn-edit-data .edit-data-progress').hide();
            $('#btn-edit-data').prop('disabled', false);

            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(field, messages) {
                    $("span." + field + "_error_edit").text(messages[0]);
                });
                Swal.fire({ title: "Error", text: xhr.responseJSON.message || "Validasi gagal.", icon: "error" });
                return;
            }

            if (xhr.status === 401) {
                Swal.fire({ title: "Unauthorized", text: xhr.responseJSON?.message || "Sesi habis.", icon: "warning" });
                return;
            }

            var msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) || "Terjadi kesalahan server.";
            Swal.fire({ title: "Error", text: msg, icon: "error" });
        }
    });

});


                // event delegation untuk tombol delete
                $("body").on("click", ".btn-get-delete", function(e) {
                    e.preventDefault();

                    var id = $(this).data("id");
                    if (!id) return console.error('ID tidak ditemukan pada tombol delete');

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
                           

                            $.ajaxSetup({
                                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                            });

                            // gunakan POST + _method DELETE agar kompatibel
                            $.ajax({
                                url: "/user-management/role/" + id,
                                method: "POST",
                                data: { _method: "DELETE" },
                                dataType: "json",
                                success: function(res) {
                                    Swal.close();
                                    if (res && (res.status === false || res.error)) {
                                        Swal.fire({ title: "Gagal", text: res.message || res.error || "Tidak dapat menghapus data.", icon: "error" });
                                        return;
                                    }
                                    // refresh datatable dan beri notifikasi
                                    $(".chimox").DataTable().ajax.reload();
                                    Swal.fire({ title: "Terhapus", text: res.message || res.success || "Data berhasil dihapus.", icon: "success", timer: 1500 });
                                },
                                error: function(xhr) {
                                    Swal.close();
                                    let msg = "Terjadi kesalahan pada server.";
                                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                                    if (xhr.status === 401) {
                                        Swal.fire({ title: "Unauthorized", text: msg, icon: "warning" });
                                    } else {
                                        Swal.fire({ title: "Error", text: msg, icon: "error" });
                                    }
                                }
                            });
                        }
                    });
                });




            });
        </script>

        <script>    
         // Make the DIV element draggable:
            var elements = document.querySelectorAll('#Modal_Tambah_Data, #Modal_Edit_Data, #Modal_Hapus_Data,#Modal_Show_Data');
            elements.forEach(function(element) {
                dragElement(element);

                function dragElement(elmnt) {
                    var pos1 = 0,
                        pos2 = 0,
                        pos3 = 0,
                        pos4 = 0;
                    if (elmnt.querySelector('.modal-header')) {
                        // if present, the header is where you move the DIV from:
                        elmnt.querySelector('.modal-header').onmousedown = dragMouseDown;
                    } else {
                        // otherwise, move the DIV from anywhere inside the DIV:
                        elmnt.onmousedown = dragMouseDown;
                    }

                    function dragMouseDown(e) {
                        e = e || window.event;
                        // get the mouse cursor position at startup:
                        pos3 = e.clientX;
                        pos4 = e.clientY;
                        document.onmouseup = closeDragElement;
                        // call a function whenever the cursor moves:
                        document.onmousemove = elementDrag;
                    }

                    function elementDrag(e) {
                        e = e || window.event;
                        // calculate the new cursor position:
                        pos1 = pos3 - e.clientX;
                        pos2 = pos4 - e.clientY;
                        pos3 = e.clientX;
                        pos4 = e.clientY;
                        // set the element's new position:
                        elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
                        elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
                    }

                    function closeDragElement() {
                        // stop moving when mouse button is released:
                        document.onmouseup = null;
                        document.onmousemove = null;
                    }
                }
            });
        </script>

    
    @endpush
@endsection