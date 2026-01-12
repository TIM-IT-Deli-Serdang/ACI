@extends('auth.app')
@section('title', 'Registrasi Akun')
@section('content')
    <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">

        <div class="bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-10 shadow-lg">

            <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-450px">

                <div class="d-flex flex-center flex-column flex-column-fluid mb-5">
                    <div class="text-center mb-8">
                        <img alt="Logo" class="h-40px h-lg-50px mb-4"
                            src="{{ asset('assets/media/logos/logo-aci.png') }}" />
                        <h1 class="text-gray-900 fw-bolder mb-3">Buat Akun Baru</h1>
                        <div class="text-gray-500 fw-semibold fs-6">Lengkapi data diri Anda untuk mendaftar</div>
                    </div>

                    <form class="form w-100" id="kt_sign_up_form">
                        @csrf

                        <div class="fv-row mb-5">
                            <label class="form-label fw-bold text-dark fs-6">Nama Lengkap</label>
                            <input type="text" placeholder="Masukkan Nama Lengkap" name="name" autocomplete="off"
                                class="form-control bg-transparent" required />
                        </div>

                        <div class="fv-row mb-5">
                            <label class="form-label fw-bold text-dark fs-6">NIK (Nomor Induk Kependudukan)</label>
                            <input type="number" placeholder="16 Digit NIK" name="nik" autocomplete="off"
                                class="form-control bg-transparent" required />
                        </div>

                        <div class="fv-row mb-5">
                            <label class="form-label fw-bold text-dark fs-6">Email (Opsional)</label>
                            <input type="email" placeholder="nama@email.com" name="email" autocomplete="off"
                                class="form-control bg-transparent" />
                        </div>

                        {{-- MODIFIKASI: Input No WA dengan Tombol OTP --}}
                        <div class="fv-row mb-5">
                            <label class="form-label fw-bold text-dark fs-6">No. WhatsApp</label>
                            <div class="input-group">
                                <input type="number" id="input_no_wa" placeholder="08xxxxxxxxxx" name="no_wa"
                                    autocomplete="off" class="form-control bg-transparent" required />
                                <button type="button" id="btn_kirim_otp" class="btn btn-light-primary">
                                    <span class="indicator-label">Kirim OTP</span>
                                    <span class="indicator-progress">
                                        <span class="spinner-border spinner-border-sm align-middle"></span>
                                    </span>
                                </button>
                            </div>
                            <div class="form-text text-muted" id="otp_timer_msg" style="display: none;">
                                Tunggu <span id="countdown" class="fw-bold text-danger">60</span> detik untuk kirim ulang.
                            </div>
                        </div>

                        {{-- MODIFIKASI: Input OTP (Hidden by Default) --}}
                        {{-- MODIFIKASI: Input OTP dengan Tombol Validasi --}}
                        <div class="fv-row mb-5 d-none" id="div_input_otp">
                            <label class="form-label fw-bold text-dark fs-6">Masukkan Kode OTP</label>
                            <div class="input-group">
                                <input type="text" id="input_otp_user" placeholder="6 Digit Kode" maxlength="6"
                                    autocomplete="off" class="form-control bg-transparent" />

                                {{-- Tombol Validasi Baru --}}
                                <button type="button" id="btn_validasi_otp" class="btn btn-success">
                                    <span class="indicator-label">Validasi</span>
                                    <span class="indicator-progress">
                                        <span class="spinner-border spinner-border-sm align-middle"></span>
                                    </span>
                                </button>
                            </div>

                            <div class="mt-1">
                                <span class="text-success fw-bold d-none" id="msg_otp_valid">
                                    <i class="ki-outline ki-check-circle fs-2 text-success"></i> Kode OTP Benar!
                                </span>
                                <span class="text-danger fw-bold d-none" id="msg_otp_invalid">
                                    <i class="ki-outline ki-cross-circle fs-2 text-danger"></i> Kode OTP Salah!
                                </span>
                            </div>
                            <div class="text-muted fs-7 mt-1">Kode OTP telah dikirim ke WhatsApp Anda.</div>
                        </div>

                        <div class="fv-row mb-5">
                            <label class="form-label fw-bold text-dark fs-6">Password</label>
                            <div class="position-relative mb-3">
                                <input class="form-control bg-transparent" type="password" placeholder="Password"
                                    name="password" autocomplete="off" required />
                                <span
                                    class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2 toggle-password"
                                    style="cursor: pointer;">
                                    <i class="ki-outline ki-eye-slash fs-2"></i>
                                    <i class="ki-outline ki-eye fs-2 d-none"></i>
                                </span>
                            </div>
                            <div class="text-muted">Gunakan 8 karakter atau lebih.</div>
                        </div>

                        <div class="fv-row mb-8">
                            <label class="form-label fw-bold text-dark fs-6">Konfirmasi Password</label>
                            <div class="position-relative mb-3">
                                <input class="form-control bg-transparent" type="password" placeholder="Ulangi Password"
                                    name="password_confirmation" autocomplete="off" required />
                                <span
                                    class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2 toggle-password"
                                    style="cursor: pointer;">
                                    <i class="ki-outline ki-eye-slash fs-2"></i>
                                    <i class="ki-outline ki-eye fs-2 d-none"></i>
                                </span>
                            </div>
                        </div>

                        {{-- Tombol Submit (Hidden sampai OTP Valid) --}}
                        <div class="d-grid mb-10 d-none" id="div_submit_btn">
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

            // --- VARIABLES ---
            let isVerified = false;
            let timerInterval = null;

            // Elements
            const btnKirimOtp = document.getElementById('btn_kirim_otp');
            const inputNoWa = document.getElementById('input_no_wa');

            const divInputOtp = document.getElementById('div_input_otp');
            const inputOtpUser = document.getElementById('input_otp_user');

            // Pastikan Anda sudah menambahkan tombol ini di HTML (sesuai panduan sebelumnya)
            const btnValidasiOtp = document.getElementById('btn_validasi_otp');

            const divSubmitBtn = document.getElementById('div_submit_btn');
            const otpTimerMsg = document.getElementById('otp_timer_msg');
            const countdownSpan = document.getElementById('countdown');

            const msgValid = document.getElementById('msg_otp_valid');
            const msgInvalid = document.getElementById('msg_otp_invalid');

            // --- 1. KIRIM OTP ---
            btnKirimOtp.addEventListener('click', async function() {
                const noWa = inputNoWa.value.trim();

                if (!noWa || noWa.length < 10) {
                    Swal.fire("Peringatan", "Masukkan Nomor WhatsApp yang valid.", "warning");
                    return;
                }

                // UI Loading
                btnKirimOtp.setAttribute('data-kt-indicator', 'on');
                btnKirimOtp.disabled = true;

                try {
                    // Panggil endpoint Kirim OTP
                    const response = await fetch("{{ route('register.send-otp') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({
                            no_wa: noWa
                        })
                    });

                    const result = await response.json();

                    if (response.ok && result.status) {
                        Swal.fire({
                            text: "Kode OTP berhasil dikirim ke WhatsApp!",
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Tampilkan input OTP
                        divInputOtp.classList.remove('d-none');
                        inputOtpUser.value = '';
                        inputOtpUser.focus();

                        // Kunci nomor WA agar tidak diubah saat proses
                        inputNoWa.readOnly = true;

                        // Mulai Timer
                        startTimer(60);

                    } else {
                        Swal.fire("Gagal", result.message || "Gagal mengirim OTP.", "error");
                        btnKirimOtp.removeAttribute('data-kt-indicator');
                        btnKirimOtp.disabled = false;
                    }

                } catch (error) {
                    console.error(error);
                    Swal.fire("Error", "Gagal menghubungi server.", "error");
                    btnKirimOtp.removeAttribute('data-kt-indicator');
                    btnKirimOtp.disabled = false;
                }
            });

            // --- 2. TIMER LOGIC ---
            function startTimer(seconds) {
                let counter = seconds;
                otpTimerMsg.style.display = 'block';
                countdownSpan.innerText = counter;

                clearInterval(timerInterval);
                timerInterval = setInterval(() => {
                    counter--;
                    countdownSpan.innerText = counter;

                    if (counter <= 0) {
                        clearInterval(timerInterval);
                        otpTimerMsg.style.display = 'none';

                        // Enable tombol kirim ulang
                        btnKirimOtp.removeAttribute('data-kt-indicator');
                        btnKirimOtp.disabled = false;
                        btnKirimOtp.querySelector('.indicator-label').innerText = "Kirim Ulang OTP";

                        // Izinkan edit nomor WA lagi jika mau
                        inputNoWa.readOnly = false;
                    }
                }, 1000);
            }

            // --- 3. VALIDASI OTP (SERVER SIDE) ---
            // Kita gunakan tombol "Validasi" yang baru, bukan keyup
            if (btnValidasiOtp) {
                btnValidasiOtp.addEventListener('click', async function() {
                    const otpCode = inputOtpUser.value.trim();
                    const noWa = inputNoWa.value.trim();

                    if (otpCode.length < 6) {
                        Swal.fire("Info", "Masukkan 6 digit kode OTP.", "info");
                        return;
                    }

                    // UI Loading
                    btnValidasiOtp.setAttribute('data-kt-indicator', 'on');
                    btnValidasiOtp.disabled = true;

                    try {
                        // Panggil endpoint Verify OTP
                        const response = await fetch("{{ route('register.verify-otp') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Content-Type": "application/json",
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({
                                otp: otpCode,
                                no_wa: noWa
                            })
                        });

                        const result = await response.json();

                        if (response.ok && result.status) {
                            // SUKSES TERVALIDASI
                            isVerified = true;

                            // Tampilkan icon ceklis
                            if (msgValid) msgValid.classList.remove('d-none');
                            if (msgInvalid) msgInvalid.classList.add('d-none');

                            // Kunci input OTP dan sembunyikan tombol validasi
                            inputOtpUser.classList.add('is-valid');
                            inputOtpUser.classList.remove('is-invalid');
                            inputOtpUser.readOnly = true;
                            btnValidasiOtp.classList.add('d-none');

                            // MUNCULKAN TOMBOL DAFTAR
                            divSubmitBtn.classList.remove('d-none');

                        } else {
                            // GAGAL
                            isVerified = false;
                            if (msgValid) msgValid.classList.add('d-none');
                            if (msgInvalid) msgInvalid.classList.remove('d-none');

                            inputOtpUser.classList.add('is-invalid');
                            Swal.fire("Gagal", result.message || "Kode OTP Salah.", "error");
                        }

                    } catch (error) {
                        console.error(error);
                        Swal.fire("Error", "Gagal memvalidasi OTP.", "error");
                    } finally {
                        btnValidasiOtp.removeAttribute('data-kt-indicator');
                        btnValidasiOtp.disabled = false;
                    }
                });
            }

            // --- 4. HANDLE PERUBAHAN NO WA ---
            // Jika user mengubah no HP, reset semua state
            inputNoWa.addEventListener('input', function() {
                if (!divInputOtp.classList.contains('d-none')) {
                    // Sembunyikan field OTP & Tombol Submit
                    divInputOtp.classList.add('d-none');
                    divSubmitBtn.classList.add('d-none');

                    // Reset state
                    isVerified = false;
                    inputOtpUser.value = '';
                    inputOtpUser.readOnly = false;
                    inputOtpUser.classList.remove('is-valid', 'is-invalid');

                    // Reset pesan validasi
                    if (msgValid) msgValid.classList.add('d-none');
                    if (msgInvalid) msgInvalid.classList.add('d-none');

                    // Munculkan kembali tombol validasi
                    if (btnValidasiOtp) btnValidasiOtp.classList.remove('d-none');

                    // Matikan timer & reset tombol kirim
                    clearInterval(timerInterval);
                    otpTimerMsg.style.display = 'none';
                    btnKirimOtp.removeAttribute('data-kt-indicator');
                    btnKirimOtp.disabled = false;
                    btnKirimOtp.querySelector('.indicator-label').innerText = "Kirim OTP";
                }
            });

            // --- 5. SHOW HIDE PASSWORD ---
            const toggleButtons = document.querySelectorAll('.toggle-password');
            toggleButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
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

            // --- 6. SUBMIT FORM UTAMA ---
            const form = document.getElementById('kt_sign_up_form');
            const submitButton = document.getElementById('kt_sign_up_submit');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Cek status verifikasi sebelum submit
                if (!isVerified) {
                    Swal.fire("Error", "Silakan validasi OTP terlebih dahulu.", "warning");
                    return;
                }

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
                            text: "Registrasi berhasil! Silakan login.",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, Login!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('login') }}";
                            }
                        });
                    } else {
                        let errorMessage = result.message || "Terjadi kesalahan.";
                        if (result.errors) {
                            const firstKey = Object.keys(result.errors)[0];
                            errorMessage = result.errors[firstKey][0];
                        }
                        Swal.fire({
                            text: errorMessage,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, Perbaiki",
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        text: "Masalah koneksi server.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                } finally {
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;
                }
            });
        });
    </script>
@endpush
