@extends('backend.layout.app')
@section('title', 'Validasi Laporan')
@section('content')

    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Validasi Laporan
                </h1>
            </div>
            <div class="d-flex align-items-center pt-4 pb-7 pt-lg-1 pb-lg-2">
                <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-secondary">
                    <i class="ki-outline ki-arrow-left fs-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="row">

            <div class="col-xl-7">

                <div class="card card-flush mb-5 mb-xl-10">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Detail Laporan</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Informasi lengkap pengaduan</span>
                        </h3>
                    </div>
                    <div class="card-body">

                        <div class="mb-5 text-center">
                            @php
                                $fotoUrl = $data['file_masyarakat']
                                    ? 'http://10.0.22.97/storage/laporan/masyarakat/' . $data['file_masyarakat']
                                    : null;
                            @endphp
                            @if ($fotoUrl)
                                <a href="{{ $fotoUrl }}" target="_blank">
                                    <img src="{{ $fotoUrl }}" class="rounded mw-100 shadow-sm"
                                        style="max-height: 300px; object-fit: cover;" alt="Bukti Laporan">
                                </a>
                            @else
                                <div class="alert alert-secondary d-flex align-items-center p-5">
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-dark">Tidak ada foto</h4>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-flush fw-semibold gy-1">
                                <tbody>
                                    <tr>
                                        <td class="text-muted min-w-125px w-125px">Kategori</td>
                                        <td class="text-gray-800">
                                            @switch($data['kategori_laporan_id'] ?? 0)
                                                @case(1)
                                                    Jalan Rusak
                                                @break

                                                @case(2)
                                                    Drainase Tersumbat
                                                @break

                                                @case(3)
                                                    Banjir
                                                @break

                                                @case(4)
                                                    Jembatan Rusak
                                                @break

                                                @case(5)
                                                    Infrastruktur Lain
                                                @break

                                                @default
                                                    -
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Tanggal Lapor</td>
                                        <td class="text-gray-800">
                                            {{ \Carbon\Carbon::parse($data['created_at'])->translatedFormat('d F Y, H:i') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Lokasi</td>
                                        <td class="text-gray-800">
                                            {{ $data['alamat'] }} <br>
                                            <small class="text-muted">Lat: {{ $data['latitude'] }}, Long:
                                                {{ $data['longitude'] }}</small>
                                            @if ($data['latitude'] && $data['longitude'])
                                                <a href="https://maps.google.com/?q={{ $data['latitude'] }},{{ $data['longitude'] }}"
                                                    target="_blank" class="ms-2 badge badge-light-primary">Lihat Peta</a>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="separator my-5"></div>

                        <div class="mb-5">
                            <label class="form-label fw-bold text-dark">Deskripsi Masalah:</label>
                            <div class="p-4 bg-light rounded text-gray-800 fw-semibold fs-6">
                                {{ $data['deskripsi'] }}
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card card-flush">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Riwayat Validasi</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline-label">

                            <div class="timeline-item">
                                <div class="timeline-label fw-bold text-gray-800 fs-6">
                                    {{ \Carbon\Carbon::parse($data['created_at'])->format('H:i') }}</div>
                                <div class="timeline-badge">
                                    <i class="fa fa-genderless text-gray-600 fs-1"></i>
                                </div>
                                <div class="fw-bold text-gray-800 ps-3">Laporan Masuk (Pengajuan)</div>
                            </div>

                            @if (!empty($data['penerima_tgl']))
                                <div class="timeline-item">
                                    <div class="timeline-label fw-bold text-gray-800 fs-6">
                                        {{ \Carbon\Carbon::parse($data['penerima_tgl'])->format('H:i') }}</div>
                                    <div class="timeline-badge">
                                        <i class="fa fa-genderless text-info fs-1"></i>
                                    </div>
                                    <div class="timeline-content d-flex flex-column ps-3">
                                        <span class="fw-bold text-gray-800">Diterima Admin & Disposisi</span>
                                        <span class="text-muted fs-7">{{ $data['penerima_keterangan'] }}</span>

                                        {{-- LOGIKA PENCARIAN NAMA UPT --}}
                                        @if (isset($data['upt']))
                                            {{-- Jika API mengirim relasi upt lengkap --}}
                                            <div
                                                class="d-flex align-items-center mt-2 p-2 bg-light-info rounded border border-info border-dashed">
                                                <span class="badge badge-info me-2">Disposisi ke</span>
                                                <span class="fw-bold text-gray-800 fs-7">
                                                    {{ $data['upt']['nama_upt'] ?? ($data['upt']['nama'] ?? 'UPT') }}
                                                </span>
                                            </div>
                                        @elseif(!empty($data['upt_id']))
                                            {{-- Jika hanya ada ID, cari namanya di listUpt --}}
                                            @php
                                                $uptName = 'ID: ' . $data['upt_id']; // Default
                                                if (!empty($listUpt)) {
                                                    $found = collect($listUpt)->firstWhere('id', $data['upt_id']);
                                                    if ($found) {
                                                        $uptName =
                                                            $found['nama_upt'] ??
                                                            ($found['nama'] ?? ($found['name'] ?? $uptName));
                                                    }
                                                }
                                            @endphp
                                            <div
                                                class="d-flex align-items-center mt-2 p-2 bg-light-info rounded border border-info border-dashed">
                                                <span class="badge badge-info me-2">Disposisi ke</span>
                                                <span class="fw-bold text-gray-800 fs-7">{{ $uptName }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if (!empty($data['verif_tgl']))
                                <div class="timeline-item">
                                    <div class="timeline-label fw-bold text-gray-800 fs-6">
                                        {{ \Carbon\Carbon::parse($data['verif_tgl'])->format('H:i') }}</div>
                                    <div class="timeline-badge">
                                        <i class="fa fa-genderless text-primary fs-1"></i>
                                    </div>
                                    <div class="timeline-content d-flex flex-column ps-3">
                                        <span class="fw-bold text-gray-800">Diverifikasi UPT</span>
                                        <span class="text-muted fs-7">{{ $data['verif_keterangan'] }}</span>
                                        @if (!empty($data['verif_file']))
                                            <a href="http://10.0.22.97/storage/verifikasi_laporan/{{ $data['verif_file'] }}"
                                                target="_blank" class="badge badge-light-primary mt-1 w-100px">
                                                <i class="ki-outline ki-file fs-4 me-1"></i> Lihat Bukti
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if (!empty($data['penanganan_tgl']))
                                <div class="timeline-item">
                                    <div class="timeline-label fw-bold text-gray-800 fs-6">
                                        {{ \Carbon\Carbon::parse($data['penanganan_tgl'])->format('H:i') }}</div>
                                    <div class="timeline-badge">
                                        <i class="fa fa-genderless text-warning fs-1"></i>
                                    </div>
                                    <div class="timeline-content d-flex flex-column ps-3">
                                        <span class="fw-bold text-gray-800">Sedang Ditangani</span>
                                        <span class="text-muted fs-7">{{ $data['penanganan_keterangan'] }}</span>
                                    </div>
                                </div>
                            @endif

                            @if (!empty($data['selesai_tgl']))
                                <div class="timeline-item">
                                    <div class="timeline-label fw-bold text-gray-800 fs-6">
                                        {{ \Carbon\Carbon::parse($data['selesai_tgl'])->format('H:i') }}</div>
                                    <div class="timeline-badge">
                                        <i class="fa fa-genderless text-success fs-1"></i>
                                    </div>
                                    <div class="timeline-content d-flex flex-column ps-3">
                                        <span class="fw-bold text-gray-800">Selesai Dikerjakan</span>
                                        <span class="text-muted fs-7">{{ $data['selesai_keterangan'] }}</span>
                                    </div>
                                </div>
                            @endif

                            @if ($data['status_laporan'] == 5)
                                <div class="timeline-item">
                                    <div class="timeline-label fw-bold text-gray-800 fs-6">-</div>
                                    <div class="timeline-badge">
                                        <i class="fa fa-genderless text-danger fs-1"></i>
                                    </div>
                                    <div class="timeline-content d-flex flex-column ps-3">
                                        <span class="fw-bold text-danger">Laporan Ditolak</span>
                                        <span class="text-muted fs-7">
                                            {{ $data['penerima_keterangan_tolak'] ??
                                                ($data['verif_keterangan_tolak'] ??
                                                    ($data['penanganan_keterangan_tolak'] ?? ($data['selesai_keterangan_tolak'] ?? 'Alasan tidak disebutkan'))) }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Aksi Validasi</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">
                                Status:
                                @switch($data['status_laporan'])
                                    @case(0)
                                        <span class="badge badge-secondary">Pengajuan</span>
                                    @break

                                    @case(1)
                                        <span class="badge badge-info">Diterima</span>
                                    @break

                                    @case(2)
                                        <span class="badge badge-primary">Diverifikasi</span>
                                    @break

                                    @case(3)
                                        <span class="badge badge-warning">Penanganan</span>
                                    @break

                                    @case(4)
                                        <span class="badge badge-success">Selesai</span>
                                    @break

                                    @case(5)
                                        <span class="badge badge-danger">Ditolak</span>
                                    @break
                                @endswitch
                            </span>
                        </h3>
                    </div>

                    <div class="card-body">
                        @php
                            $status = (int) $data['status_laporan'];
                            $showForm = false;

                            if ($status == 0 && ($isSuperAdmin || $isSda)) {
                                $showForm = true;
                            } elseif ($status == 1 && ($isSuperAdmin || $isUpt)) {
                                $showForm = true;
                            } elseif ($status == 2 && ($isSuperAdmin || $isSda)) {
                                $showForm = true;
                            } elseif ($status == 3 && ($isSuperAdmin || $isSda)) {
                                $showForm = true;
                            }

                            if ($status == 4 || $status == 5) {
                                $showForm = false;
                            }
                        @endphp

                        @if ($showForm)
                            <form id="form_validasi" action="{{ route('laporan.process-validation', $data['id']) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="current_status" value="{{ $status }}">

                                <div class="mb-5">
                                    <label class="required form-label fw-bold">Keputusan</label>
                                    <select name="action" id="action_select" class="form-select" required>
                                        <option value="">Pilih Aksi...</option>
                                        @if ($status == 0)
                                            <option value="next">Terima & Assign UPT</option>
                                            <option value="reject">Tolak Laporan</option>
                                        @elseif($status == 1)
                                            <option value="next">Verifikasi & Setujui</option>
                                            <option value="reject">Tolak Laporan</option>
                                        @elseif($status == 2)
                                            <option value="next">Lanjut ke Penanganan</option>
                                            <option value="reject">Tolak Laporan</option>
                                        @elseif($status == 3)
                                            <option value="next">Selesaikan Laporan</option>
                                            <option value="reject">Tolak Laporan</option>
                                        @endif
                                    </select>
                                </div>

                                {{-- [BARU] INPUT SELECT UPT (Hanya muncul di Status 0) --}}
                                @if ($status == 0)
                                    <div class="mb-5 d-none" id="div_upt_select">
                                        <label class="required form-label fw-bold">Pilih UPT Penanggung Jawab</label>
                                        <select name="upt_id" class="form-select" data-control="select2"
                                            data-placeholder="Pilih UPT...">
                                            <option></option>
                                            @if (!empty($listUpt))
                                                @foreach ($listUpt as $upt)
                                                    <option value="{{ $upt['id'] }}">
                                                        {{ $upt['nama_upt'] ?? ($upt['nama'] ?? 'UPT Tanpa Nama') }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="text-muted fs-7">Laporan akan diteruskan ke UPT ini untuk diverifikasi.
                                        </div>
                                    </div>
                                @endif

                                {{-- INPUT FILE VERIFIKASI (Hanya Status 1) --}}
                                @if ($status == 1)
                                    <div class="mb-5 d-none" id="div_verif_file">
                                        <label class="required form-label fw-bold">Upload Bukti Verifikasi</label>

                                        {{-- Tambahkan accept untuk video --}}
                                        <input type="file" name="verif_file" class="form-control"
                                            accept=".pdf, .jpg, .jpeg, .png, .mp4, .mov, .avi, .mkv, .webm">

                                        <div class="text-muted fs-7 mt-1">
                                            Wajib diisi jika menyetujui.<br>
                                            <span class="text-danger">*</span> Foto/PDF Max 2MB. Video Max 120MB.
                                        </div>
                                    </div>
                                @endif

                                @if ($status == 3)
                                    <div class="mb-5 d-none" id="div_selesai_file">
                                        <label class="required form-label fw-bold">Upload Bukti Penyelesaian</label>

                                        <input type="file" name="selesai_file" class="form-control"
                                            accept=".jpg, .jpeg, .png">

                                        <div class="text-muted fs-7 mt-1">
                                            Wajib diisi sebagai bukti pekerjaan selesai.<br>
                                            <span class="text-danger">*</span> Format Gambar (JPG, JPEG, PNG) Max 2MB.
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-10">
                                    <label class="required form-label fw-bold">Catatan / Keterangan</label>
                                    <textarea name="keterangan" class="form-control" rows="4" placeholder="Berikan alasan..." required></textarea>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" id="btn_submit_validasi" class="btn btn-primary">
                                        <span class="indicator-label"><i class="ki-outline ki-check-circle fs-2"></i>
                                            Simpan Validasi</span>
                                        <span class="indicator-progress">Mohon tunggu... <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                </div>
                            </form>
                        @else
                            <div
                                class="alert alert-dismissible bg-light-primary border border-primary d-flex flex-column flex-sm-row w-100 p-5 mb-10">
                                <i class="ki-outline ki-information-5 fs-2hx text-primary me-4 mb-5 mb-sm-0"></i>
                                <div class="d-flex flex-column pe-0 pe-sm-10">
                                    <h5 class="mb-1">Tidak ada aksi tersedia</h5>
                                    <span>
                                        @if ($status == 4)
                                            Laporan ini sudah <b>Selesai</b>.
                                        @elseif($status == 5)
                                            Laporan ini sudah <b>Ditolak</b>.
                                        @else
                                            Anda tidak memiliki hak akses untuk memproses status ini.
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {

                // Logic Show/Hide Inputs
                $('#action_select').on('change', function() {
                    var val = $(this).val();

                    // Status 0: Show/Hide UPT Select
                    if ($('#div_upt_select').length) {
                        if (val === 'next') {
                            $('#div_upt_select').removeClass('d-none');
                            $('select[name="upt_id"]').prop('required', true);
                        } else {
                            $('#div_upt_select').addClass('d-none');
                            $('select[name="upt_id"]').prop('required', false);
                        }
                    }

                    // Status 1: Show/Hide File Upload
                    if ($('#div_verif_file').length) {
                        if (val === 'next') {
                            $('#div_verif_file').removeClass('d-none');
                            $('input[name="verif_file"]').prop('required', true);
                        } else {
                            $('#div_verif_file').addClass('d-none');
                            $('input[name="verif_file"]').prop('required', false);
                        }
                    }

                    if ($('#div_selesai_file').length) {
                        if (val === 'next') {
                            $('#div_selesai_file').removeClass('d-none');
                            $('input[name="selesai_file"]').prop('required', true);
                        } else {
                            $('#div_selesai_file').addClass('d-none');
                            $('input[name="selesai_file"]').prop('required', false);
                        }
                    }
                });

                // Spinner Button
                $('#form_validasi').on('submit', function() {
                    var btn = $('#btn_submit_validasi');
                    btn.attr('data-kt-indicator', 'on');
                    btn.prop('disabled', true);
                });

                // SweetAlert
                @if (session('success'))
                    Swal.fire({
                        title: "Berhasil!",
                        text: "{{ session('success') }}",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                @endif
                @if (session('error'))
                    Swal.fire({
                        title: "Gagal!",
                        text: "{{ session('error') }}",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                @endif
            });
        </script>
    @endpush

@endsection
