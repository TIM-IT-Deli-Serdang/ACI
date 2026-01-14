@extends('auth.app')
@section('title', 'Reset Password')
@section('content')
    <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
        <div class="bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-10 shadow-lg">
            <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">

                {{-- LOGO --}}
                <div class="d-flex flex-center flex-column flex-column-fluid mb-5">
                    <div class="theme-light-show d-flex align-items-center gap-4">
                        <img alt="Logo SDA" class="h-40px h-lg-45px" src="{{ asset('assets/media/logos/logo-sda.png') }}" />
                        <img alt="Logo DS" class="h-40px h-lg-45px" src="{{ asset('assets/media/logos/logo-ds.png') }}" />
                    </div>
                </div>

                {{-- FORM --}}
                <div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20 my-12">
                    <form class="form w-100" id="kt_new_password_form">
                        @csrf
                        <div class="text-center mb-10">
                            <h1 class="text-dark fw-bolder mb-3">Atur Password Baru</h1>
                            <div class="text-gray-500 fw-semibold fs-6">
                                Kami telah mengirimkan kode OTP ke <strong>{{ $login }}</strong>
                            </div>
                        </div>

                        <input type="hidden" name="login" value="{{ $login }}">

                        {{-- OTP --}}
                        <div class="fv-row mb-8">
                            <label class="form-label fw-bolder text-dark fs-6">Kode OTP</label>
                            <input class="form-control bg-transparent" type="text" placeholder="Masukkan Kode OTP"
                                name="otp" autocomplete="off" required />
                            <div class="text-muted fs-7 mt-1">Cek WhatsApp Anda untuk Melihat OTP</div>
                        </div>

                        {{-- PASSWORD BARU --}}
                        <div class="fv-row mb-8" data-kt-password-meter="true">
                            <label class="form-label fw-bolder text-dark fs-6">Password Baru</label>
                            <div class="position-relative mb-3">
                                <input class="form-control bg-transparent" type="password" placeholder="Password Baru"
                                    name="password" autocomplete="off" required />
                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                    data-kt-password-meter-control="visibility">
                                    <i class="ki-outline ki-eye-slash fs-2"></i>
                                </span>
                            </div>
                        </div>

                        {{-- CONFIRM PASSWORD --}}
                        <div class="fv-row mb-8">
                            <label class="form-label fw-bolder text-dark fs-6">Ulangi Password</label>
                            <input class="form-control bg-transparent" type="password" placeholder="Ulangi Password Baru"
                                name="password_confirmation" autocomplete="off" required />
                        </div>

                        <div class="d-grid mb-10">
                            <button type="submit" id="kt_new_password_submit" class="btn btn-primary">
                                <span class="indicator-label">Ubah Password</span>
                                <span class="indicator-progress">Mohon tunggu...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Script Toggle Password Visibility manual jika metronic JS tidak jalan
        document.querySelectorAll('[data-kt-password-meter-control="visibility"]').forEach(item => {
            item.addEventListener('click', e => {
                const input = item.previousElementSibling;
                const icon = item.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('ki-eye-slash');
                    icon.classList.add('ki-eye');
                } else {
                    input.type = 'password';
                    icon.classList.remove('ki-eye');
                    icon.classList.add('ki-eye-slash');
                }
            });
        });

        document.getElementById('kt_new_password_form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('kt_new_password_submit');
            const label = btn.querySelector('.indicator-label');
            const progress = btn.querySelector('.indicator-progress');

            // UI Loading
            btn.classList.add('disabled');
            label.style.display = 'none';
            progress.style.display = 'inline-block';

            const formData = new FormData(this);

            try {
                const response = await fetch("{{ route('password.update') }}", {
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
                        title: 'Sukses!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('login') }}";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: result.message || 'OTP salah atau validasi gagal.',
                    });
                }

            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan sistem.',
                });
            } finally {
                btn.classList.remove('disabled');
                label.style.display = 'block';
                progress.style.display = 'none';
            }
        });
    </script>
@endpush
