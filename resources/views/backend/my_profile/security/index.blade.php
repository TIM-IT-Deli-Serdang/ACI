@extends('backend.my_profile.index')
@section('title', 'Security')
@section('mp')
@php
    $user = session('user');
@endphp
    <!--begin::details View-->
    <div class="card mb-5 mb-xl-10 border border-gray-300" id="kt_profile_details_view">
        <!--begin::Card header-->
        <div class="card-header cursor-pointer border-bottom border-gray-300 bg-secondary">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">Sign In Method</h3>
            </div>
            <!--end::Card title-->
            <!--begin::Action-->
            <a class="btn btn-sm btn-primary align-self-center" id="getEditRowData" data-id="{{ $user['id'] }}">Change
                Password</a>
            <!--end::Action-->
        </div>
        <!--begin::Card header-->
        <!--begin::Card body-->
        <div class="card-body ">

            <!--begin::Password-->
            <div>
                <!--begin::Label-->

                <div class="fw-bold text-gray-600">


                    <div class="row mb-1">
                        <div class="col-lg-4">
                            <!--begin::Label-->
                            <div id="kt_signin_email">
                                <div class="fs-6 fw-bolder text-dark mb-1">Email Address</div>
                                <div class="fw-bold text-gray-600">{{ $user['email'] }}</div>
                            </div>
                            <!--end::Label-->
                        </div>
                        <div class="col-lg-4">
                            <!--begin::Label-->
                            <div id="kt_signin_email">
                                <div class="fs-6 fw-bolder text-dark mb-1">Last Login</div>
                                <div class="fw-bold text-gray-600">{{ $user['last_login_at'] }}</div>
                            </div>
                            <!--end::Label-->
                        </div>
                        <div class="col-lg-4">
                            <!--begin::Label-->
                            <div id="kt_signin_email">
                                <div class="fs-6 fw-bolder text-dark mb-1">Last IP Address</div>
                                <div class="fw-bold text-gray-600">{{ $user['last_login_ip'] }}</div>
                            </div>
                            <!--end::Label-->
                        </div>
                    </div>



                </div>



            </div>
            <!--end::Password-->

        </div>
        <!--end::Card body-->
    </div>
    <!--end::details View-->


    <!-- Edit Article Modal -->
    <div class="modal fade" id="Modal_Edit_Data" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-750px">
            <div class="modal-content" id="edit-modal-content">
                <div class="modal-header border-bottom border-gray-300" id="kt_modal_edit_user_header">
                    <h2 class="fw-bold">Change Password</h2>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1 text-dark"></i>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body px-5 my-7">
                    <form id="FormEditModalID" class="form" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf



                        <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_edit_user_scroll"
                            data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                            data-kt-scroll-dependencies="#kt_modal_edit_user_header"
                            data-kt-scroll-wrappers="#kt_modal_edit_user_scroll" data-kt-scroll-offset="300px">
                            <div class="fv-row mb-7" id="EditRowModalBody"></div>
                        </div>

                        <div class="text-center pt-10">
                            <button type="button" class="btn btn-sm btn-secondary me-3"
                                data-bs-dismiss="modal">Discard</button>
                            <button type="submit" class="btn btn-sm btn-primary" value="submit" id="btn-change-password">
                                <span class="indicator-label">Submit</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('stylesheets')
        <meta name="csrf-token" content="{{ csrf_token() }}">
    @endpush

    @push('scripts')
        <script type="text/javascript">
            $(document).ready(function() {
    var targetedit = document.querySelector("#edit-modal-content");
    var blockUIEdit = new KTBlockUI(targetedit, {
        message: '<div class="blockui-message"><span class="spinner-border text-danger"></span> <span class="text-white">Mohon Sabar, Data Sedang Proses...</span></div>',
        overlayClass: "bg-dark bg-opacity-50",
    });

    var submitButton = document.querySelector("#btn-change-password");

    function resetSubmitButton() {
        submitButton.removeAttribute("data-kt-indicator");
        submitButton.disabled = false;
    }

    function showEditModal(result) {
        $('#EditRowModalBody').html(result.html);
        $('#Modal_Edit_Data').modal('show');
    }

    function showErrorMessage(title, text) {
        Swal.fire({ title: title, text: text, icon: "error", timer: 1500, confirmButtonText: "Ok" });
    }

    // load edit form via AJAX
    $('body').on('click', '#getEditRowData', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        blockUIEdit.block(); // show loading while fetching
        $.ajax({
            url: "my-security/" + id + "/edit",
            dataType: "json",
            method: "GET",
            success: function(result) {
                showEditModal(result);
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Gagal memuat form edit.';
                showErrorMessage('Error', msg);
            },
            complete: function() {
                blockUIEdit.release();
            }
        });
    });

    // submit form
    $('#FormEditModalID').on('submit', function(e) {
        e.preventDefault();
        submitButton.setAttribute("data-kt-indicator", "on");
        submitButton.disabled = true;

        var id = $('#hidden_id').val();
        var form = this;
        var fd = new FormData(form);

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        blockUIEdit.block(); // block UI during submit

        $.ajax({
            url: "my-security/" + id,
            method: "POST", // _method=PUT included by @method('PUT') in form HTML
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            beforeSend: function() {
                $(document).find("span.error-text").text("");
            },
            success: function(result) {

    // 🔐 password berubah → paksa logout
    if (result.force_logout) {
        $("#Modal_Edit_Data").modal("hide");

        Swal.fire({
            text: result.success || 'Password berhasil diubah. Silakan login ulang.',
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: "Login Ulang",
            customClass: { confirmButton: "btn btn-primary" }
        }).then(() => {
            window.location.href = '/login';
        });

        return; // ⛔ stop di sini
    }

    // ❌ VALIDATION ERROR
    if (result.errors) {
        $.each(result.errors, function(prefix, val) {
            var field = prefix.split('.')[0];
            $("span." + field + "_error_edit").text(val[0]);
        });
        showErrorMessage("Error", "Terjadi kesalahan validasi, periksa kembali input Anda.");
        return;
    }

    // ❌ ERROR UMUM
    if (result.error) {
        $("#Modal_Edit_Data").modal("hide");
        showErrorMessage(result.judul || "Error", result.error);
        return;
    }

    // ✅ SUCCESS NORMAL (bukan password)
    $("#Modal_Edit_Data").modal("hide");

    Swal.fire({
        text: result.success || 'Berhasil diperbarui.',
        icon: "success",
        buttonsStyling: false,
        confirmButtonText: "Ok, got it!",
        timer: 1500,
        customClass: { confirmButton: "btn btn-primary" }
    });

    setTimeout(function() {
        location.reload();
    }, 900);
},
            error: function(xhr) {
                var msg = 'Terjadi kesalahan pada server.';
                if (xhr.responseJSON) {
                    msg = xhr.responseJSON.error || xhr.responseJSON.message || msg;
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(prefix, val) {
                            var field = prefix.split('.')[0];
                            $("span." + field + "_error_edit").text(val[0]);
                        });
                        msg = 'Periksa kembali input Anda.';
                    }
                }
                showErrorMessage('Error', msg);
            },
            complete: function() {
                blockUIEdit.release();
                resetSubmitButton();
            }
        });
    });

    // close btn (jika ada button bertanda .modelClose)
    $(document).on('click', '.modelClose', function() {
        $('#Modal_Edit_Data').modal('hide');
    });
});


            // Make the DIV element draggable:
            document.querySelectorAll('#Modal_Edit_Data').forEach(function(element) {
                dragElement(element);

                function dragElement(elmnt) {
                    let pos1 = 0,
                        pos2 = 0,
                        pos3 = 0,
                        pos4 = 0;
                    const header = elmnt.querySelector('.modal-header');

                    if (header) {
                        // Only make the header draggable
                        header.onmousedown = dragMouseDown;
                    }

                    function dragMouseDown(e) {
                        e.preventDefault();
                        pos3 = e.clientX;
                        pos4 = e.clientY;
                        document.onmouseup = closeDragElement;
                        document.onmousemove = elementDrag;
                    }

                    function elementDrag(e) {
                        e.preventDefault();
                        pos1 = pos3 - e.clientX;
                        pos2 = pos4 - e.clientY;
                        pos3 = e.clientX;
                        pos4 = e.clientY;

                        // Move the modal
                        elmnt.style.position = "absolute";
                        elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
                        elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
                    }

                    function closeDragElement() {
                        // Stop moving when mouse button is released
                        document.onmouseup = null;
                        document.onmousemove = null;
                    }
                }
            });
        </script>
    @endpush
@endsection
