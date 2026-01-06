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

                        <div class="fv-row mb-5">
                            <label class="form-label fw-bold text-dark fs-6">Email (Opsional)</label>
                            <input type="email" placeholder="nama@email.com" name="email" autocomplete="off" class="form-control bg-transparent" />
                        </div>

                        {{-- MODIFIKASI: Input No WA dengan Tombol OTP --}}
                        <div class="fv-row mb-5">
                            <label class="form-label fw-bold text-dark fs-6">No. WhatsApp</label>
                            <div class="input-group">
                                <input type="number" id="input_no_wa" placeholder="08xxxxxxxxxx" name="no_wa" autocomplete="off" class="form-control bg-transparent" required />
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
                        <div class="fv-row mb-5 d-none" id="div_input_otp">
                            <label class="form-label fw-bold text-dark fs-6">Masukkan Kode OTP</label>
                            <div class="position-relative">
                                <input type="text" id="input_otp_user" placeholder="6 Digit Kode" maxlength="6" autocomplete="off" class="form-control bg-transparent" />
                                <span class="position-absolute top-50 end-0 translate-middle-y me-3">
                                    <i class="ki-outline ki-check-circle fs-1 text-success d-none" id="icon_otp_valid"></i>
                                    <i class="ki-outline ki-cross-circle fs-1 text-danger d-none" id="icon_otp_invalid"></i>
                                </span>
                            </div>
                            <div class="text-muted fs-7 mt-1">Kode OTP telah dikirim ke WhatsApp Anda.</div>
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
                            <div class="text-muted">Gunakan 8 karakter atau lebih.</div>
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
        
        // --- VARIABLES FOR OTP ---
        let generatedOTP = null;
        let isVerified = false;
        let timerInterval = null;
        const apiBaseUrl = "{{ rtrim(env('API_BASE_URL', ''), '/') }}"; // Ambil base url dari .env via blade

        const btnKirimOtp = document.getElementById('btn_kirim_otp');
        const inputNoWa = document.getElementById('input_no_wa');
        const divInputOtp = document.getElementById('div_input_otp');
        const inputOtpUser = document.getElementById('input_otp_user');
        const divSubmitBtn = document.getElementById('div_submit_btn');
        const otpTimerMsg = document.getElementById('otp_timer_msg');
        const countdownSpan = document.getElementById('countdown');
        const iconValid = document.getElementById('icon_otp_valid');
        const iconInvalid = document.getElementById('icon_otp_invalid');

        // --- 1. GENERATE & SEND OTP ---
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
        // [PERUBAHAN DISINI]
        // Panggil route web.php (Local Frontend)
        // Kita tidak kirim 'otp' dari JS, karena OTP digenerate di Server (Controller) agar aman
        const response = await fetch("{{ route('register.send-otp') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}", // Wajib karena ini route Web
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
                }
            }, 1000);
        }

        // --- 3. VERIFIKASI INPUT OTP ---
        inputOtpUser.addEventListener('keyup', function() {
            const val = this.value.trim();

            if (val.length === 6) {
                if (val === generatedOTP) {
                    // SUKSES
                    isVerified = true;
                    iconValid.classList.remove('d-none');
                    iconInvalid.classList.add('d-none');
                    divSubmitBtn.classList.remove('d-none'); // Munculkan tombol daftar
                    inputOtpUser.classList.add('is-valid');
                    inputOtpUser.classList.remove('is-invalid');
                } else {
                    // GAGAL
                    isVerified = false;
                    iconValid.classList.add('d-none');
                    iconInvalid.classList.remove('d-none');
                    divSubmitBtn.classList.add('d-none');
                    inputOtpUser.classList.add('is-invalid');
                    inputOtpUser.classList.remove('is-valid');
                }
            } else {
                // Belum 6 digit
                iconValid.classList.add('d-none');
                iconInvalid.classList.add('d-none');
                divSubmitBtn.classList.add('d-none');
                inputOtpUser.classList.remove('is-valid', 'is-invalid');
            }
        });

        // --- 4. HANDLE PERUBAHAN NO WA ---
        // Jika user mengubah no HP setelah OTP terkirim/terverifikasi, reset ulang
        inputNoWa.addEventListener('input', function() {
            if (!divInputOtp.classList.contains('d-none')) {
                // Sembunyikan field OTP & Tombol Submit
                divInputOtp.classList.add('d-none');
                divSubmitBtn.classList.add('d-none');
                
                // Reset state
                isVerified = false;
                generatedOTP = null;
                inputOtpUser.value = '';
                
                // Matikan timer & reset tombol kirim
                clearInterval(timerInterval);
                otpTimerMsg.style.display = 'none';
                btnKirimOtp.removeAttribute('data-kt-indicator');
                btnKirimOtp.disabled = false;
                btnKirimOtp.querySelector('.indicator-label').innerText = "Kirim OTP";
            }
        });

        // --- 5. LOGIC SHOW HIDE PASSWORD (BAWAAN) ---
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

            // Cek verifikasi OTP terakhir kali sebelum submit (security layer frontend)
            if (!isVerified || inputOtpUser.value !== generatedOTP) {
                Swal.fire("Error", "Nomor WhatsApp belum terverifikasi dengan benar.", "error");
                return;
            }

            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            const formData = new FormData(form);

            try {
                // Ganti URL ke route register controller Anda
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
                    text: "Masalah koneksi server.",
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