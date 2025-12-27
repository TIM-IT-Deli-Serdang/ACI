@extends('auth.app')
@section('title', 'Login')
@section('content')
    <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">

        <div class="bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-10 shadow-lg">

            <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">

                <div class="d-flex flex-center flex-column flex-column-fluid mb-2">
    
                    <div class="theme-light-show d-flex align-items-center gap-4">
                        <img alt="Logo SDA" class="h-40px h-lg-45px" 
                             src="{{ asset('assets/media/logos/logo-sda.png') }}" />
                        
                        <img alt="Logo DS" class="h-40px h-lg-45px" 
                             src="{{ asset('assets/media/logos/logo-ds.png') }}" />
                    </div>
                    <div class="theme-dark-show d-flex align-items-center gap-4">
                        <img alt="Logo SDA" class="h-40px h-lg-45px" 
                             src="{{ asset('assets/media/logos/logo-sda.png') }}" />
                        
                        <img alt="Logo DS" class="h-40px h-lg-45px" 
                             src="{{ asset('assets/media/logos/logo-ds.png') }}" />
                    </div>
                    </div>

                <div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20 my-12">



                    <form class="form w-100" id="kt_sign_in_form" >
                        @csrf

                        <div class="text-center mb-11 pb-8">
                            <h1 class="text-gray-900 fw-bolder mb-3">Sign In</h1>
                            <div class="text-gray-500 fw-semibold fs-6">Masuk menggunakan akun terdaftar</div>
                        </div>

                        <div class="fv-row mb-8">
                           <input type="text" placeholder="Email atau No WA" name="login" autocomplete="off" 
                           class="form-control bg-transparent" value="{{ old('login') }}" />

                        </div>

                        <div class="fv-row mb-3">
                            <input type="password" placeholder="Password" name="password" autocomplete="off"
                                class="form-control bg-transparent" />
                        </div>

                     

                        <div class="d-grid mb-10">
                            <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                <span class="indicator-label">Sign In</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>

                    </form>

                </div>

                <div class="d-flex flex-stack">
                    <div class="me-10">
                        <span class="text-muted fw-semibold me-1">{{ date('Y') }}</span>
                        <a class="text-gray-800 text-hover-primary">&copy; IT Deli Serdang</a>
                    </div>
                    <div class="d-flex fw-semibold text-muted fs-base gap-5">
                        <span class="px-2">ALPHA VERSION</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
function shortMsg(msg, max = 400) {
    if (msg === undefined || msg === null) return '';
    msg = String(msg);
    if (msg.length <= max) return msg;
    return msg.slice(0, max - 3) + '...';
}

function formatErrorsObject(errors) {
    if (!errors || typeof errors !== 'object') return '';
    try {
        return Object.entries(errors).map(([k, v]) => {
            if (Array.isArray(v)) return `${k}: ${v[0]}`;
            if (typeof v === 'string') return `${k}: ${v}`;
            return `${k}: ${JSON.stringify(v)}`;
        }).join('\n');
    } catch (e) {
        return String(errors);
    }
}

document.getElementById('kt_sign_in_form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const submitButton = document.getElementById('kt_sign_in_submit');
    const indicatorLabel = submitButton.querySelector('.indicator-label');
    const indicatorProgress = submitButton.querySelector('.indicator-progress');

    const setLoading = (loading = true) => {
        if (loading) {
            submitButton.classList.add("disabled");
            if (indicatorLabel) indicatorLabel.style.display = 'none';
            if (indicatorProgress) indicatorProgress.style.display = 'inline-block';
        } else {
            submitButton.classList.remove("disabled");
            if (indicatorLabel) indicatorLabel.style.display = 'block';
            if (indicatorProgress) indicatorProgress.style.display = 'none';
        }
    };
    setLoading(true);

    const form = this;
    const loginVal = form.querySelector('[name="login"]').value.trim();
    const passVal = form.querySelector('[name="password"]').value.trim();
    if (!loginVal || !passVal) {
        Swal.fire({ icon: 'warning', title: 'Isi Form', text: 'Harap isi login dan password.' });
        setLoading(false);
        return;
    }

    const formData = new FormData(form);

    try {
        const response = await fetch("{{ route('login') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: formData,
            credentials: 'same-origin'
        });

        // jika fetch di-redirect (mis. Laravel redirect to login view), tangani sebagai error
        if (response.redirected) {
            // Ambil text (HTML) tapi tampilkan hanya teks bersih (strip tags)
            const html = await response.text();
            const stripped = (new DOMParser()).parseFromString(html, 'text/html').body.textContent || '';
            const short = stripped.trim().replace(/\s+/g, ' ').slice(0, 300) || 'Response redirected to HTML. Login gagal.';
            Swal.fire({ icon: 'error', title: 'Login Gagal!', text: short, confirmButtonColor: '#d33' });
            setLoading(false);
            return;
        }

        // cek content-type header
        const contentType = response.headers.get('content-type') || '';
        let result = {};
        if (contentType.includes('application/json')) {
            // aman parse json
            try { result = await response.json(); }
            catch (e) { result = { message: 'Invalid JSON response from server.' }; }
        } else {
            // server returned HTML / text (theme) — do NOT inject HTML into modal
            const txt = await response.text();
            const textOnly = (new DOMParser()).parseFromString(txt, 'text/html').body.textContent || txt;
            // coba cari pesan API sederhana di dalam text (optional)
            const firstLine = textOnly.trim().replace(/\s+/g, ' ').slice(0, 500);
            Swal.fire({ icon: 'error', title: 'Login Gagal!', text: firstLine || 'Server returned HTML. Login gagal.', confirmButtonColor: '#d33' });
            setLoading(false);
            return;
        }

        console.log('LOGIN RESULT:', response.status, result);

        // ===== validation 422 =====
        if (response.status === 422) {
            const errors = result.errors ?? result;
            const first = (errors && (errors.login?.[0] || errors.password?.[0])) || result.message || Object.values(errors)[0];
            Swal.fire({ icon: 'error', title: 'Login Gagal!', text: String(first).slice(0, 500), confirmButtonColor: '#d33' });
            setLoading(false);
            return;
        }

        // ===== too many attempts (429) =====
        if (response.status === 429) {
            const msg = result.message ?? 'Terlalu banyak percobaan, coba lagi nanti.';
            const secondsMatch = String(msg).match(/\d+/);
            const seconds = secondsMatch ? parseInt(secondsMatch[0], 10) : 0;
            if (seconds > 0) showLockoutCountdown(seconds);
            else Swal.fire({ icon: 'warning', title: 'Terlalu Banyak Percobaan', text: msg, confirmButtonColor: '#d33' });
            setLoading(false);
            return;
        }

        // ===== other non-ok =====
        if (!response.ok) {
            const apiMsg = result.message ?? result.error ?? JSON.stringify(result.errors ?? result);
            Swal.fire({ icon: 'error', title: 'Login Gagal!', text: String(apiMsg).slice(0, 500), confirmButtonColor: '#d33' });
            setLoading(false);
            return;
        }

        // ===== success: verify token/status =====
        const hasToken = result.token || result.access_token;
        const statusTrue = result.status === true;
        if (!hasToken && !statusTrue) {
            Swal.fire({ icon: 'error', title: 'Login Gagal!', text: String(result.message ?? 'Token tidak diterima').slice(0, 500), confirmButtonColor: '#d33' });
            setLoading(false);
            return;
        }

        // sukses -> loader & redirect
        superPremiumThreeDotLoader();

    } catch (err) {
        console.error('Fetch/login error', err);
        Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: 'Tidak dapat menghubungi server.', confirmButtonColor: '#d33' });
        setLoading(false);
    }
});


/* ---------- lockout countdown UI ---------- */
function showLockoutCountdown(seconds) {
    let originalSeconds = seconds;

    Swal.fire({
        icon: "warning",
        title: "Terlalu Banyak Percobaan!",
        html: `
            Anda telah gagal login beberapa kali.<br>
            Coba lagi dalam <b id="countdown">${seconds}</b> detik.
            <br><br>
            <div class="progress" style="height:10px; border-radius:12px; background:#f1f5f9;">
                <div id="lock-progress" class="progress-bar" style="width:0%; height:100%; background:#ef4444; border-radius:12px;"></div>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false,
        timer: seconds * 1000,
        didOpen: () => {
            let countdownEl = document.getElementById("countdown");
            let bar = document.getElementById("lock-progress");
            let interval = setInterval(() => {
                seconds--;
                countdownEl.textContent = seconds;
                let percent = 100 - Math.floor((seconds / originalSeconds) * 100);
                bar.style.width = percent + "%";
                if (seconds <= 0) clearInterval(interval);
            }, 1000);
        }
    });
}

/* ---------- loader (tidy + bounded) ---------- */
function superPremiumThreeDotLoader() {
    let timerInterval;
    const STYLE_ID = 'sa-super-premium-styles';

    if (!document.getElementById(STYLE_ID)) {
        const style = document.createElement('style');
        style.id = STYLE_ID;
        style.textContent = `
            .sa-popup { width:420px !important; max-width:92% !important; padding:1.2rem !important; box-sizing:border-box; }
            .sa-html { max-height:240px; overflow:auto; text-align:center; }
            .sa-loader-row { display:flex; justify-content:center; align-items:center; gap:10px; margin:12px 0 18px; }
            .dot-loader { width:12px; height:12px; background:#22c55e; border-radius:50%; animation:bounceDot 0.6s infinite alternate; }
            .dot-loader--2 { animation-delay:0.12s; } .dot-loader--3 { animation-delay:0.24s; }
            @keyframes bounceDot { 0%{transform:translateY(0);opacity:1}100%{transform:translateY(-8px);opacity:0.45} }
            .sa-progress { height:10px; border-radius:20px; overflow:hidden; margin-top:8px; background:rgba(0,0,0,0.06); }
            .sa-progress > .bar { height:100%; width:0%; border-radius:20px; background:#16a34a; transition:width 120ms linear; }
            .sa-percent { margin-top:8px; font-weight:600; color:#374151; }
        `;
        document.head.appendChild(style);
    }

    Swal.fire({
        icon: "success",
        title: `<span class="fw-bold">Login Berhasil</span>`,
        html: `
            <div class="sa-html">
                <div class="text-muted" style="margin-bottom:8px">Menyiapkan aplikasi untuk Anda...</div>
                <div class="sa-loader-row" aria-hidden="true">
                    <div class="dot-loader"></div>
                    <div class="dot-loader dot-loader--2"></div>
                    <div class="dot-loader dot-loader--3"></div>
                </div>
                <div class="sa-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div id="sa-progress-premium" class="bar"></div>
                </div>
                <div id="sa-percent" class="sa-percent">0%</div>
            </div>
        `,
        customClass: { popup: 'sa-popup', htmlContainer: 'sa-html' },
        width: 'auto',
        padding: '1.2rem',
        color: '#000',
        timer: 2200,
        showConfirmButton: false,
        didOpen: () => {
            document.body.style.transition = "filter .4s";
            const bar = document.getElementById("sa-progress-premium");
            const percentText = document.getElementById("sa-percent");
            if (!bar || !percentText) return;
            let width = 0;
            const duration = 2200;
            const steps = 100;
            const interval = Math.max(10, Math.floor(duration / steps));
            timerInterval = setInterval(() => {
                width = Math.min(100, width + 1);
                bar.style.width = width + "%";
                percentText.textContent = width + "%";
                if (width >= 100) clearInterval(timerInterval);
            }, interval);
        },
        willClose: () => {
            clearInterval(timerInterval);
            document.body.style.filter = "none";
        }
    }).then(() => {
        const container = document.querySelector(".d-flex.flex-column-fluid");
        if (container) {
            container.style.transition = "opacity .45s ease-in-out";
            container.style.opacity = '0';
        }
        setTimeout(() => {
            window.location.href = "{{ route('dashboard.index') }}";
        }, 450);
    });
}
</script>
@endpush
