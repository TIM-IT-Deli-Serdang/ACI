@extends('auth.app')
@section('title', 'Registrasi Akun')
@section('content')
    <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">

        <div class="bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-10 shadow-lg">

            <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-450px">

                <div class="d-flex flex-center flex-column flex-column-fluid mb-5">
                    <div class="text-center mb-8">
                        <img alt="Logo" class="h-40px h-lg-50px mb-4" src="{{ asset('assets/media/logos/logo-aci.png') }}" />
                        <h1 class="text-gray-900 fw-bolder mb-3">Buat Akun Baru</h1>
                        <div class="text-gray-500 fw-semibold fs-6">Lengkapi data diri Anda untuk mendaftar</div>
                    </div>

                    <form class="form w-100" id="kt_sign_up_form">
                        @csrf

                        <div class="fv-row mb-5">
                            <label class="form-label fw-bold text-dark fs-6">Nama Lengkap</label>
                            <input type="text" placeholder="Masukkan Nama Lengkap" name="name" autocomplete="off" class="form-control bg-transparent" required />
                        </div>

                        <div class="fv-row mb-5">
                            <label class="form-label fw-bold text-dark fs-6">NIK (Nomor Induk Kependudukan)</label>
                            <input type="number" placeholder="16 Digit NIK" name="nik" autocomplete="off" class="form-control bg-transparent" required />
                        </div>

                        <div class="row fv-row mb-5">
                            <div class="col-xl-6">
                                <label class="form-label fw-bold text-dark fs-6">Email (Opsional)</label>
                                <input type="email" placeholder="nama@email.com" name="email" autocomplete="off" class="form-control bg-transparent" />
                            </div>
                            <div class="col-xl-6">
                                <label class="form-label fw-bold text-dark fs-6">No. WhatsApp</label>
                                <input type="number" placeholder="08xxxxxxxxxx" name="no_wa" autocomplete="off" class="form-control bg-transparent" required />
                            </div>
                        </div>

                        <div class="fv-row mb-5">
                            <label class="form-label fw-bold text-dark fs-6">Password</label>
                            <div class="position-relative mb-3">
                                <input class="form-control bg-transparent" type="password" placeholder="Password" name="password" autocomplete="off" required />
                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2 toggle-password" style="cursor: pointer;">
                                    <i class="ki-outline ki-eye-slash fs-2"></i>
                                    <i class="ki-outline ki-eye fs-2 d-none"></i>
                                </span>
                            </div>
                            <div class="text-muted">Gunakan 8 karakter atau lebih dengan campuran huruf, angka & simbol.</div>
                        </div>

                        <div class="fv-row mb-8">
                            <label class="form-label fw-bold text-dark fs-6">Konfirmasi Password</label>
                            <div class="position-relative mb-3">
                                <input class="form-control bg-transparent" type="password" placeholder="Ulangi Password" name="password_confirmation" autocomplete="off" required />
                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2 toggle-password" style="cursor: pointer;">
                                    <i class="ki-outline ki-eye-slash fs-2"></i>
                                    <i class="ki-outline ki-eye fs-2 d-none"></i>
                                </span>
                            </div>
                        </div>

                        <div class="d-grid mb-10">
                            <button type="submit" id="kt_sign_up_submit" class="btn btn-primary">
                                <span class="indicator-label">Daftar Sekarang</span>
                                <span class="indicator-progress">Mohon tunggu... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>

                        <div class="text-gray-500 text-center fw-semibold fs-6">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="link-primary fw-bold">Masuk di sini</a>
                        </div>
                    </form>
                </div>

                <div class="d-flex flex-stack">
                    <div class="me-10">
                        <span class="text-muted fw-semibold me-1">{{ date('Y') }}</span>
                        <a class="text-gray-800 text-hover-primary">&copy; IT Deli Serdang</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Script Show/Hide Password (Universal untuk kedua field)
        const toggleButtons = document.querySelectorAll('.toggle-password');
        
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                // Cari input yang satu parent dengan tombol ini
                const input = this.closest('.position-relative').querySelector('input');
                const iconSlash = this.querySelector('.ki-eye-slash');
                const iconEye = this.querySelector('.ki-eye');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    iconSlash.classList.add('d-none');
                    iconEye.classList.remove('d-none');
                } else {
                    input.type = 'password';
                    iconSlash.classList.remove('d-none');
                    iconEye.classList.add('d-none');
                }
            });
        });

        // 2. Form Submit Handler (Sama seperti sebelumnya)
        const form = document.getElementById('kt_sign_up_form');
        const submitButton = document.getElementById('kt_sign_up_submit');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // UI Loading
            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            const formData = new FormData(form);

            try {
                const response = await fetch("{{ route('register.submit') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.status) {
                    Swal.fire({
                        text: "Registrasi berhasil! Silakan login dengan akun baru Anda.",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, Login!",
                        customClass: { confirmButton: "btn btn-primary" }
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('login') }}";
                        }
                    });
                } else {
                    let errorMessage = result.message || "Terjadi kesalahan.";
                    if(result.errors) {
                        const firstKey = Object.keys(result.errors)[0];
                        errorMessage = result.errors[firstKey][0];
                    }

                    Swal.fire({
                        text: errorMessage,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, Perbaiki",
                        customClass: { confirmButton: "btn btn-danger" }
                    });
                }

            } catch (error) {
                Swal.fire({
                    text: "Maaf, sepertinya ada masalah koneksi server.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: { confirmButton: "btn btn-primary" }
                });
            } finally {
                submitButton.removeAttribute('data-kt-indicator');
                submitButton.disabled = false;
            }
        });
    });
</script>
@endpush