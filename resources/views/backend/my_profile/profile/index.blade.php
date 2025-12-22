@extends('backend.my_profile.index')
@section('title', 'My Profile')
@section('mp')
@php
    $user = session('user');
@endphp
    <div class="card mb-5 mb-xl-10 shadow-sm border border-gray-300 ">
        <!--begin::Card header-->
        <div class="card-header cursor-pointer border-bottom border-gray-300 bg-secondary">
            <!--begin::Card title-->
            <div class="card-title m-0 ">
                <h3 class="fw-bold m-0">Profile Details</h3>
            </div>
            <!--end::Card title-->
            <!--begin::Action-->
           <a class="btn btn-sm btn-primary align-self-center" id="getEditRowData"
                        data-id="{{ $user->id ?? $user['id'] ?? '' }}">Edit Profile</a>
            <!--end::Action-->
        </div>
        <!--begin::Card header-->
        <!--begin::Card body-->
        <div class="card-body p-9">
            <!--begin::Row-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted">Full Name</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8">
                    <span
                        class="fw-bold fs-6 text-gray-800 profile-name">{{ ucwords(strtolower($user['name'])) }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted">WhatsApp</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8 fv-row">
                    <span class="fw-semibold text-gray-800 fs-6 profile-no-wa">{{ $user['no_wa'] }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->

            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-semibold text-muted">Email</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8 fv-row">
                    <span class="fw-semibold text-gray-800 fs-6 profile-email">{{ $user['email'] }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->


        </div>
        <!--end::Card body-->
    </div>



    <!-- Edit Article Modal -->
    <div class="modal fade" id="Modal_Edit_Data" data-bs-backdrop="static" data-bs-focus="false" data-bs-keyboard="false"
     tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content" id="edit-modal-content">
            <div class="modal-header border-bottom border-gray-300" id="kt_modal_edit_role_header">
                <h2 class="fw-bold">Edit Profile</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary " data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1 text-dark"></i>
                </div>
            </div>

            <div class="modal-body px-5 my-7">
                {{-- Form akan di-inject oleh AJAX (view partial). Tetap sediakan struktur dasar --}}
                <form id="FormEditModalID" class="form" enctype="multipart/form-data">
                    @method('PUT') @csrf
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_edit_role_scroll"
                         data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                         data-kt-scroll-dependencies="#kt_modal_edit_role_header"
                         data-kt-scroll-wrappers="#kt_modal_edit_role_scroll" data-kt-scroll-offset="300px">
                        {{-- Konten form dari server --}}
                        <div class="fv-row mb-7" id="EditRowModalBody">
                            {{-- Jika partial tidak menyertakan hidden_id, tempatkan fallback hidden --}}
                            <input type="hidden" id="hidden_id" name="hidden_id" value="{{ $user->id ?? $user['id'] ?? '' }}">
                        </div>
                        <input type="hidden" name="action" id="action" />
                    </div>

                    <div class="text-center pt-10">
                        <button type="button" class="btn btn-sm btn-secondary me-3" data-bs-dismiss="modal">Discard</button>
                        <button type="submit" class="btn btn-sm btn-primary" value="submit" id="btn-change-profile">
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
        message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> <span class="text-white">Please Wait ...</span></div>',
        overlayClass: "bg-dark bg-opacity-50",
    });

    // Helper: clear previous validation errors
    function clearErrorsEdit() {
        $('#EditRowModalBody').find('span.field-error').each(function(){ $(this).text(''); });
        $('#EditRowModalBody').find('.is-invalid').removeClass('is-invalid');
    }

    // Event untuk mengambil data saat tombol edit diklik
    $('body').on('click', '#getEditRowData', function(e) {
        e.preventDefault();
        clearErrorsEdit();
        var id = $(this).data('id');

        if (!id) {
            return Swal.fire({ icon: 'error', title: 'Error', text: 'ID user tidak tersedia.' });
        }

        var url = "{{ url('my-profile') }}/" + id + "/edit";

        $.ajax({
            url: url,
            dataType: "json",
            beforeSend: function(){ blockUIEdit.block(); },
            complete: function(){ blockUIEdit.release(); },
            success: function(result) {
                if (result.html) {
                    $('#EditRowModalBody').html(result.html);
                    // pastikan ada hidden_id di form (partial harus menyertakan)
                    if (!$('#hidden_id').length) {
                        $('#FormEditModalID').prepend('<input type="hidden" id="hidden_id" name="hidden_id" value="'+id+'">');
                    } else {
                        $('#hidden_id').val(id);
                    }
                    $('#Modal_Edit_Data').modal('show');
                } else if (result.error) {
                    Swal.fire({ icon: 'error', title: 'Error', text: result.error });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Respons tidak valid dari server.' });
                }
            },
            error: function(xhr) {
                var text = xhr.status + ' ' + xhr.statusText;
                try {
                    var j = xhr.responseJSON;
                    if (j && j.error) text = j.error;
                } catch(e){}
                Swal.fire({ icon: 'error', title: 'Gagal', text: text });
            }
        });
    });

    // Update data via Ajax
    $('#FormEditModalID').on('submit', function(e) {
        e.preventDefault();
        clearErrorsEdit();

        var submitButton = document.querySelector("#btn-change-profile");
        submitButton.setAttribute("data-kt-indicator", "on");
        submitButton.disabled = true;
        blockUIEdit.block();

        // Ambil id dari hidden_id atau data-id fallback
        var id = $('#hidden_id').val() || $('a#getEditRowData').data('id') || '';

        if (!id) {
            submitButton.removeAttribute("data-kt-indicator");
            submitButton.disabled = false;
            blockUIEdit.release();
            return Swal.fire({ icon: 'error', title: 'Error', text: 'ID user tidak tersedia.' });
        }

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Gunakan FormData supaya _method dan file bisa ikut
        var form = this;
        var fd = new FormData(form);

        // Jika partial edit tidak menyediakan _method, pastikan kita meng-override method ke PUT
        if (!fd.get('_method')) {
            fd.append('_method', 'PUT');
        }

        // POST ke route web yang akan mproxy ke backend
        var url = "{{ url('my-profile') }}/" + id;

        $.ajax({
            url: url,
            method: "POST",
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(result) {
                handleResponse(result, submitButton);
            },
            error: function(xhr) {
                var resp = {};
                try { resp = xhr.responseJSON || {}; } catch(e){}

                // Validation errors (Laravel returns 422 with { errors: { field: [..] } })
                if (xhr.status === 422 && resp.errors) {
                    // tampilkan per-field
                    $.each(resp.errors, function(field, messages) {
                        var selector = 'span.' + field + '_error_edit';
                        var $el = $(selector);
                        if ($el.length) {
                            $el.text(messages[0]);
                            // tandai input
                            $('#EditRowModalBody').find('[name="'+field+'"]').addClass('is-invalid');
                        } else {
                            // fallback: cari input by name lalu append small.error
                            var $input = $('#EditRowModalBody').find('[name="'+field+'"]');
                            if ($input.length) {
                                $input.addClass('is-invalid');
                                if ($input.next('span.field-error').length === 0) {
                                    $input.after('<span class="field-error text-danger small">'+messages[0]+'</span>');
                                } else {
                                    $input.next('span.field-error').text(messages[0]);
                                }
                            }
                        }
                    });

                    Swal.fire({ icon: 'error', title: 'Validation Error', text: 'Periksa input Anda.' });
                } else {
                    var msg = resp.error || resp.message || (xhr.status + ' ' + xhr.statusText);
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                }

                submitButton.removeAttribute("data-kt-indicator");
                submitButton.disabled = false;
                blockUIEdit.release();
            }
        });
    });

    // Fungsi untuk menangani response sukses dari server
    function handleResponse(result, submitButton) {
        // Hapus indikator & unblock UI di akhir
        var finalize = function() {
            submitButton.removeAttribute("data-kt-indicator");
            submitButton.disabled = false;
            blockUIEdit.release();
        };

        if (!result) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Respons kosong dari server.' });
            return finalize();
        }

        if (result.errors) {
            // tampilkan errors (object)
            $.each(result.errors, function(prefix, val) {
                var selector = "span." + prefix + "_error_edit";
                if ($(selector).length) {
                    $(selector).text(val[0]);
                } else {
                    // tambahkan error pada input jika perlu
                    var $input = $('#EditRowModalBody').find('[name="'+prefix+'"]');
                    if ($input.length && $input.next('span.field-error').length === 0) {
                        $input.after('<span class="field-error text-danger small">'+val[0]+'</span>');
                        $input.addClass('is-invalid');
                    }
                }
            });

            Swal.fire({ title: "Error", text: "Terjadi kesalahan validasi, periksa kembali input Anda.", icon: "error", timer: 2000 });
            return finalize();
        }

        if (result.error) {
            $("#Modal_Edit_Data").modal("hide");
            Swal.fire({ title: result.judul || 'Error', text: result.error, icon: "error", timer: 1800 }).then(finalize);
            return;
        }

        // Success
        Swal.fire({
            text: result.success || 'Perubahan berhasil disimpan.',
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: "Ok",
            timer: 1500,
            customClass: { confirmButton: "btn btn-primary" },
        });

        $("#Modal_Edit_Data").modal("hide");

        // ================================
        // UPDATE PROFIL TANPA RELOAD
        // ================================
       if (result.updated) {
    // helper ucwords
    function ucwords(str) {
        return (str || '').toString().toLowerCase().replace(/\b\w/g, function(c) { return c.toUpperCase(); });
    }

    var name = result.updated.name || '';
    var firstLetter = name.length ? name.charAt(0).toUpperCase() : '';

    // === UPDATE TEXT FIELDS ===
    if (result.updated.name) {
        $(".profile-name").text(ucwords(result.updated.name));
        $(".navbar-name").text(ucwords(result.updated.name));
        $(".navbar-avatar-name").text(ucwords(result.updated.name));
        $(".navbar-avatar-sidebar-name").text(ucwords(result.updated.name));
        $(".navbar-sidebar-name").text(ucwords(result.updated.name));
        $(".account-name").text(ucwords(result.updated.name));
    }
    if (result.updated.no_wa) {
        $(".profile-no-wa").text(result.updated.no_wa);
        $(".navbar-sidebar-no_wa").text(result.updated.no_wa);
        $(".account-no_wa").text(result.updated.no_wa);
    }
    if (result.updated.email) {
        $(".profile-email").text(result.updated.email);
        $(".account-email").text(result.updated.email);
    }

    // === AVATAR HANDLING ===
    // Selectors from your blade:
    var $navbarImg = $('.navbar-avatar-img');              // <img> in navbar (if exists)
    var $sidebarImg = $('.sidebar-avatar-img');            // <img> in sidebar (if exists)
    var $navbarLabel = $('.navbar-avatar-wrapper.navbar-avatar-name'); // letter div in navbar
    var $sidebarLabel = $('.sidebar-avatar-wrapper.navbar-avatar-sidebar-name'); // letter div in sidebar

    // Avatar value returned by backend (may be filename or full URL)
    var avatar = result.updated.avatar ?? null;

    function buildAvatarUrl(filenameOrUrl) {
        if (!filenameOrUrl) return null;
        // if looks like full url, return as-is
        if (/^https?:\/\//i.test(filenameOrUrl)) return filenameOrUrl;
        // if begins with /storage or storage path, return as-is
        if (/^\/storage\//i.test(filenameOrUrl)) return filenameOrUrl;
        // else assume storage path under /storage/user/avatar/
        return '/storage/user/avatar/' + filenameOrUrl;
    }

    if (avatar) {
        var url = buildAvatarUrl(avatar) + '?t=' + Date.now(); // cache-buster

        // NAVBAR image
        if ($navbarImg.length) {
            $navbarImg.attr('src', url).show();
            if ($navbarLabel.length) $navbarLabel.hide();
        } else if ($navbarLabel.length) {
            // create image element after label and hide label
            $navbarLabel.after('<img class="navbar-avatar-img" src="'+url+'" alt="'+(name||'avatar')+'" />');
            $navbarLabel.hide();
        }

        // SIDEBAR image
        if ($sidebarImg.length) {
            $sidebarImg.attr('src', url).show();
            if ($sidebarLabel.length) $sidebarLabel.hide();
        } else if ($sidebarLabel.length) {
            $sidebarLabel.after('<img class="sidebar-avatar-img" src="'+url+'" alt="'+(name||'avatar')+'" />');
            $sidebarLabel.hide();
        }
    } else {
        // No avatar -> show initial letter and hide images (if present)
        if ($navbarLabel.length) {
            $navbarLabel.text(firstLetter).show();
        }
        if ($sidebarLabel.length) {
            $sidebarLabel.text(firstLetter).show();
        }
        if ($navbarImg.length) $navbarImg.hide();
        if ($sidebarImg.length) $sidebarImg.hide();
    }
}


        finalize();
    }
});
</script>

        <script>
            //DRAG MODAL

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
