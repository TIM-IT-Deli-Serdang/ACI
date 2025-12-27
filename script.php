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
                       
                      {
                            data: 'avatar',
                            name: 'avatar',
                            orderable: false,
                            searchable: false
                        },
                        { data: 'nik', name: 'nik' }, // Tambahan NIK
{ 
    data: 'upt.nama_upt',     // Tambahan UPT (Asumsi API kirim relasi upt)
    name: 'upt.nama_upt', 
    orderable: false, 
    searchable: false,
    defaultContent: '-'       // Jika null tampilkan -
},
                        {
                            data: 'roles',
                            name: 'roles',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'last_login_at',
                            name: 'last_login_at',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'last_login_ip',
                            name: 'last_login_ip',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'joined_date',
                            name: 'joined_date',
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

                $('#FormTambahModalID').on('submit', function(event) {
                event.preventDefault();
                blockUI.block();

                $('#btn-add-data .add-data-label').hide();
                $('#btn-add-data .add-data-progress').show();
                $('#btn-add-data').prop('disabled', true);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
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
                    success: function(result, textStatus, xhr) {
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
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        // Pastikan blockUI & button selalu di-release
                        blockUI.release();
                        $('#btn-add-data .add-data-label').show();
                        $('#btn-add-data .add-data-progress').hide();
                        $('#btn-add-data').prop('disabled', false);

                        var res = xhr.responseJSON;

                        if (xhr.status === 422 && res && res.errors) {
                            $.each(res.errors, function(field, messages) {
                                var selector = "span." + field + "_error_add";
                                $(selector).text(messages[0]);
                            });

                            Swal.fire({
                                title: "Gagal",
                                text: res.message || "Terjadi kesalahan validasi, periksa kembali input Anda.",
                                icon: "error",
                                timer: 1800,
                                confirmButtonText: "Oke",
                            });

                            return;
                        }

                        if (xhr.status === 401) {
                            Swal.fire({
                                title: "Unauthorized",
                                text: res?.message || "Anda belum login atau sesi habis.",
                                icon: "warning",
                                confirmButtonText: "Oke",
                            }).then(() => {
                                // optional redirect ke login
                                // window.location = '{{ route("login") }}';
                            });
                            return;
                        }

                        // fallback: tampilkan pesan error umum
                        var message = (res && (res.message || res.error)) || errorThrown || "Terjadi kesalahan pada server.";
                        Swal.fire({
                            title: "Error",
                            text: message,
                            icon: "error",
                            confirmButtonText: "Oke",
                        });
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
                        url: "/user-management/user/" + id + "/edit",   // sesuaikan route
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
                $("body").on("submit", "#FormEditModalID", function(e) {
                    e.preventDefault();

                    // block UI
                    blockUIEdit.block();
                    $('#btn-edit-data .edit-data-label').hide();
                    $('#btn-edit-data .edit-data-progress').show();
                    $('#btn-edit-data').prop('disabled', true);

                    var form = this;
                    var formData = new FormData(form);

                    // pastikan ambil id dari field hidden_id (yang kita set saat buka modal)
                    var id = $("#hidden_id").val() || formData.get('id');

                    // tambahkan method override untuk PUT
                    formData.append('_method', 'PUT');

                    $.ajaxSetup({
                        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") }
                    });

                    $.ajax({
                        url: "/user-management/user/" + id,  
                        method: "POST",              
                        data: formData,
                        contentType: false,
                        processData: false,
                        cache: false,
                        dataType: "json",
                        beforeSend: function() {
                            $(document).find("span.error-text").text("");
                        },
                        success: function(result) {
                            // handle validation errors dari API (422)
                            if (result.errors) {
                                blockUIEdit.release();
                                $.each(result.errors, function(field, messages) {
                                    $("span." + field + "_error_edit").text(messages[0]);
                                });
                                Swal.fire({ title: "Error", text: result.message || "Validasi gagal.", icon: "error", timer: 1500 });
                                $('#btn-edit-data .edit-data-label').show();
                                $('#btn-edit-data .edit-data-progress').hide();
                                $('#btn-edit-data').prop('disabled', false);
                                return;
                            }

                            // jika backend mengembalikan error umum
                            if (result.error || result.status === false) {
                                blockUIEdit.release();
                                $("#Modal_Edit_Data").modal("hide");
                                Swal.fire({ title: result.judul || "Error", text: result.error || result.message || "Terjadi kesalahan.", icon: "error", timer: 1500 });
                                $('#btn-edit-data .edit-data-label').show();
                                $('#btn-edit-data .edit-data-progress').hide();
                                $('#btn-edit-data').prop('disabled', false);
                                return;
                            }

                            // sukses
                            blockUIEdit.release();
                            var modalEl = document.getElementById('Modal_Edit_Data');
                            if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();
                            $(".chimox").DataTable().ajax.reload();
                            Swal.fire({ text: result.success || "Berhasil diupdate.", icon: "success", timer: 1500 });

                            $('#btn-edit-data .edit-data-label').show();
                            $('#btn-edit-data .edit-data-progress').hide();
                            $('#btn-edit-data').prop('disabled', false);
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
                                url: "/user-management/user/" + id,
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


 <script>
            $(document).ready(function() {

                //  select province:start
                $('#roles').select2({
                    dropdownParent: $("#Modal_Tambah_Data"),

                    allowClear: true,
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
                    // Di dalam $(document).ready(...)

$('#upt_id_add').select2({
    dropdownParent: $("#Modal_Tambah_Data"),
    allowClear: true,
    width: '100%',
    ajax: {
        url: "{{ route('user.select-upt') }}", // Panggil route baru tadi
        dataType: 'json',
        delay: 250,
        processResults: function(data) {
            return {
                results: data // Format sudah id & text dari controller
            };
        }
    }
});
                });
            });
        </script>

    
    @endpush