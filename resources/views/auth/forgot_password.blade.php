@extends('auth.app')
@section('title', 'Lupa Password')
@section('content')
    <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
        <div class="bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-10 shadow-lg">
            <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">

                {{-- HEADER LOGO --}}
                <div class="d-flex flex-center flex-column flex-column-fluid mb-5">
                    <div class="theme-light-show d-flex align-items-center gap-4">
                        <img alt="Logo SDA" class="h-40px h-lg-45px" src="{{ asset('assets/media/logos/logo-sda.png') }}" />
                        <img alt="Logo DS" class="h-40px h-lg-45px" src="{{ asset('assets/media/logos/logo-ds.png') }}" />
                    </div>
                    <div class="theme-dark-show d-flex align-items-center gap-4">
                        <img alt="Logo SDA" class="h-40px h-lg-45px" src="{{ asset('assets/media/logos/logo-sda.png') }}" />
                        <img alt="Logo DS" class="h-40px h-lg-45px" src="{{ asset('assets/media/logos/logo-ds.png') }}" />
                    </div>
                </div>

                {{-- FORM CONTENT --}}
                <div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20 my-12">
                    <form class="form w-100" id="kt_password_reset_form">
                        @csrf
                        <div class="text-center mb-10">
                            <h1 class="text-dark fw-bolder mb-3">Lupa Password?</h1>
                            <div class="text-gray-500 fw-semibold fs-6">
                                Masukkan No. WhatsApp Anda untuk mereset password.
                            </div>
                        </div>

                        <div class="fv-row mb-8">
                            <input type="text" placeholder="Masukkan No. WhatsApp" name="login" autocomplete="off"
                                class="form-control bg-transparent" required />
                        </div>

                        <div class="d-flex flex-wrap justify-content-center pb-lg-0">
                            <button type="submit" id="kt_password_reset_submit" class="btn btn-primary me-4">
                                <span class="indicator-label">Kirim OTP</span>
                                <span class="indicator-progress">Mohon tunggu...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                            <a href="{{ route('login') }}" class="btn btn-light">Batal</a>
                        </div>

                        {{-- [BARU] LINK SUDAH PUNYA OTP --}}
                        <div class="text-center mt-5">
                            <div class="text-gray-500 fs-7 mb-2">Sudah menerima kode OTP?</div>
                            <a href="#" id="btn-already-has-otp" class="link-primary fs-6 fw-bold">
                                Masukkan Kode OTP
                            </a>
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
        // 1. HANDLER KIRIM OTP (Tombol Biru)
        document.getElementById('kt_password_reset_form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('kt_password_reset_submit');
            const label = btn.querySelector('.indicator-label');
            const progress = btn.querySelector('.indicator-progress');

            btn.classList.add('disabled');
            label.style.display = 'none';
            progress.style.display = 'inline-block';

            const formData = new FormData(this);

            try {
                const response = await fetch("{{ route('password.otp') }}", {
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
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        confirmButtonText: 'Lanjut Masukkan OTP',
                        allowOutsideClick: false
                    }).then((r) => {
                        if (r.isConfirmed) {
                            window.location.href = "{{ route('password.reset') }}";
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: result.message || 'Terjadi kesalahan.',
                    });
                }

            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Tidak dapat menghubungi server.'
                });
            } finally {
                btn.classList.remove('disabled');
                label.style.display = 'block';
                progress.style.display = 'none';
            }
        });

        // 2. [BARU] HANDLER SUDAH PUNYA OTP
        document.getElementById('btn-already-has-otp').addEventListener('click', async function(e) {
            e.preventDefault();

            const inputLogin = document.querySelector('input[name="login"]');
            const noWa = inputLogin.value.trim();

            if (!noWa) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nomor Kosong',
                    text: 'Masukkan Nomor WhatsApp Anda di kolom input terlebih dahulu agar kami bisa memverifikasi akun Anda.',
                });
                return;
            }

            // UI Loading
            Swal.fire({
                title: 'Memeriksa Akun...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const formData = new FormData();
                formData.append('login', noWa);

                const response = await fetch("{{ route('password.verify-user') }}", {
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
                        icon: 'success',
                        title: 'Akun Ditemukan',
                        text: 'Silakan masukkan kode OTP yang Anda miliki.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('password.reset') }}";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Ditemukan',
                        text: result.message || 'Nomor tidak terdaftar.',
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan sistem.'
                });
            }
        });
    </script>
@endpush
