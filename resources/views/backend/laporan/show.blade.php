<div class="row g-5">
    {{-- BAGIAN KIRI: DETAIL UTAMA --}}
    <div class="col-md-7">
        <div class="card card-flush h-100">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">Informasi Laporan</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="fv-row mb-7">
                    <label class="fw-semibold fs-7 mb-2 text-muted">Kategori Laporan</label>
                    @php
                        $kategori = match ($data['kategori_laporan_id'] ?? 0) {
                            1 => 'Jalan Rusak',
                            2 => 'Drainase Tersumbat',
                            3 => 'Banjir',
                            4 => 'Tanggul / Jembatan Rusak',
                            5 => 'Infrastruktur Lainnya',
                            default => 'Lainnya',
                        };
                    @endphp
                    <div class="fw-bold fs-6 text-gray-800">{{ $kategori }}</div>
                </div>

                <div class="fv-row mb-7">
                    <label class="fw-semibold fs-7 mb-2 text-muted">Deskripsi Masalah</label>
                    <div class="p-4 bg-light rounded text-gray-800 fw-semibold fs-6">
                        {{ $data['deskripsi'] ?? '-' }}
                    </div>
                </div>

                <div class="fv-row mb-7">
                    <label class="fw-semibold fs-7 mb-2 text-muted">Alamat Lokasi</label>
                    <div class="fw-bold fs-6 text-gray-800">{{ $data['alamat'] ?? '-' }}</div>
                </div>
                <div class="fv-row mb-7">
                    <label class="fw-semibold fs-7 mb-2 text-muted">Kecamatan</label>
                    <div class="fw-bold fs-6 text-gray-800">{{ $data['kecamatan']['nama'] ?? '-' }}</div>
                </div>
                <div class="fv-row mb-7">
                    <label class="fw-semibold fs-7 mb-2 text-muted">Kelurahan</label>
                    <div class="fw-bold fs-6 text-gray-800">{{ $data['kelurahan']['nama'] ?? '-' }}</div>
                </div>

                <div class="row">
                    <div class="col-6 mb-7">
                        <label class="fw-semibold fs-7 mb-2 text-muted">Latitude</label>
                        <div class="fw-bold fs-6 text-gray-800">{{ $data['latitude'] ?? '-' }}</div>
                    </div>

                    <div class="col-6 mb-7">
                        <label class="fw-semibold fs-7 mb-2 text-muted">Longitude</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="fw-bold fs-6 text-gray-800">{{ $data['longitude'] ?? '-' }}</div>
                            @if (!empty($data['latitude']) && !empty($data['longitude']))
                                <a href="https://maps.google.com/?q={{ $data['latitude'] }},{{ $data['longitude'] }}"
                                    target="_blank" class="btn btn-sm btn-light-primary btn-icon h-25px w-25px"
                                    title="Lihat Peta">
                                    <i class="ki-outline ki-map fs-4"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN KANAN: STATUS & FOTO --}}
    <div class="col-md-5">
        <div class="card card-flush h-100">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">Bukti & Status</span>
                </h3>
            </div>
            <div class="card-body">
                @php
                    $storageBaseUrl = 'https://apiaci-deliserdangsehat.deliserdangkab.go.id/storage/';
                @endphp

                <div class="fv-row mb-7">
                    <div class="border rounded p-2 text-center bg-light">
                        @if (!empty($data['file_masyarakat']))
                            {{-- [UPDATE PATH] Menyesuaikan dengan store: laporan/masyarakat/{id}/file/{nama_file} --}}
                            @php
                                $filePath = 'laporan/masyarakat/' . $data['id'] . '/' . $data['file_masyarakat'];
                                $fullUrl = $storageBaseUrl . $filePath;
                            @endphp

                            <a href="{{ $fullUrl }}" target="_blank">
                                <img src="{{ $fullUrl }}" class="img-fluid rounded shadow-sm"
                                    style="max-height: 200px; width: 100%; object-fit: cover;" alt="Foto Laporan">
                            </a>
                            <div class="mt-2">
                                <a href="{{ $fullUrl }}" target="_blank"
                                    class="btn btn-sm btn-light-primary w-100">
                                    <i class="ki-outline ki-eye fs-3"></i> Lihat Full Size
                                </a>
                            </div>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center py-10 text-muted">
                                <i class="ki-outline ki-picture fs-3x mb-2"></i>
                                <span>Tidak ada foto</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="fv-row mb-7">
                    <label class="fw-semibold fs-7 mb-2 text-muted">Status Saat Ini</label>
                    <div>
                        @switch($data['status_laporan'] ?? 0)
                            @case(1)
                                <span class="badge badge-info badge-lg p-3 w-100 justify-content-center">Diterima Admin</span>
                            @break

                            @case(2)
                                <span class="badge badge-primary badge-lg p-3 w-100 justify-content-center">Diverifikasi
                                    UPT</span>
                            @break

                            @case(3)
                                <span class="badge badge-warning badge-lg p-3 w-100 justify-content-center text-dark">Sedang
                                    Ditangani</span>
                            @break

                            @case(4)
                                <span class="badge badge-success badge-lg p-3 w-100 justify-content-center">Selesai
                                    Dikerjakan</span>
                            @break

                            @case(5)
                                <span class="badge badge-danger badge-lg p-3 w-100 justify-content-center">Ditolak</span>
                            @break

                            @default
                                <span class="badge badge-secondary badge-lg p-3 w-100 justify-content-center text-dark">Laporan
                                    Masuk (Pending)</span>
                        @endswitch
                    </div>
                </div>

                <div class="fv-row">
                    <label class="fw-semibold fs-7 mb-2 text-muted">Tanggal Dilaporkan</label>
                    <div class="fw-bold fs-6 text-gray-800">
                        {{ \Carbon\Carbon::parse($data['created_at'])->translatedFormat('d F Y, H:i') }} WIB
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN BAWAH: TRACKING TIMELINE (HORIZONTAL) --}}
    <div class="col-12">
        <div class="card card-flush border border-gray-300">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">
                        <i class="ki-outline ki-route fs-2 me-2 text-primary"></i> Tracking Laporan
                    </span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">Riwayat perjalanan penyelesaian laporan</span>
                </h3>
            </div>
            <div class="card-body">
                {{-- Container Horizontal Scroll --}}
                <div class="table-responsive pb-5">
                    <div class="d-flex flex-nowrap align-items-start">

                        {{-- 1. LAPORAN MASUK --}}
                        <div class="flex-shrink-0 me-4 w-300px">
                            <div class="card border border-dashed border-gray-400 bg-light-primary h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="badge badge-primary me-2">1</span>
                                        <span class="fw-bold text-gray-800">Laporan Dibuat</span>
                                    </div>
                                    <div class="text-muted fs-7 mb-2">
                                        <i class="ki-outline ki-calendar fs-6 me-1"></i>
                                        {{ \Carbon\Carbon::parse($data['created_at'])->translatedFormat('d M Y, H:i') }}
                                    </div>
                                    <p class="fs-7 text-gray-700 mb-0">Laporan berhasil dikirim oleh pelapor.</p>
                                </div>
                            </div>
                        </div>

                        {{-- PANAH PENGHUBUNG --}}
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center h-100 me-4 pt-10">
                            <i class="ki-outline ki-arrow-right fs-2x text-gray-400"></i>
                        </div>

                        {{-- 2. DITERIMA ADMIN --}}
                        @if (!empty($data['penerima_tgl']))
                            <div class="flex-shrink-0 me-4 w-300px">
                                <div class="card border border-dashed border-gray-400 bg-light-info h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="badge badge-info me-2">2</span>
                                            <span class="fw-bold text-gray-800">Diterima & Disposisi</span>
                                        </div>
                                        <div class="text-muted fs-7 mb-2">
                                            <i class="ki-outline ki-calendar fs-6 me-1"></i>
                                            {{ \Carbon\Carbon::parse($data['penerima_tgl'])->translatedFormat('d M Y, H:i') }}
                                        </div>
                                        <p class="fs-7 text-gray-700 mb-2">{{ $data['penerima_keterangan'] }}</p>
                                        @if (isset($data['upt']) || !empty($data['upt_id']))
                                            <div class="bg-white rounded p-2 border border-gray-200">
                                                <span class="text-info fw-bold fs-8">
                                                    <i class="ki-outline ki-send fs-6 me-1"></i> Disposisi ke UPT
                                                </span>
                                                <span class="fw-bold text-gray-800 fs-7">
                                                    {{ $data['upt']['nama_upt'] ?? ($data['upt']['nama'] ?? 'UPT') }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- PANAH --}}
                            <div
                                class="flex-shrink-0 d-flex align-items-center justify-content-center h-100 me-4 pt-10">
                                <i class="ki-outline ki-arrow-right fs-2x text-gray-400"></i>
                            </div>
                        @endif

                        {{-- 3. VERIFIKASI --}}
                        @if (!empty($data['verif_tgl']))
                            <div class="flex-shrink-0 me-4 w-300px">
                                <div class="card border border-dashed border-gray-400 bg-light-primary h-100">
                                    <div class="card-body p-4">
                                        {{-- Header Step --}}
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="badge badge-primary me-2">3</span>
                                            <span class="fw-bold text-gray-800">Diverifikasi UPT</span>
                                        </div>
                                        {{-- Tanggal --}}
                                        <div class="text-muted fs-7 mb-2">
                                            <i class="ki-outline ki-calendar fs-6 me-1"></i>
                                            {{ \Carbon\Carbon::parse($data['verif_tgl'])->translatedFormat('d M Y, H:i') }}
                                        </div>
                                        {{-- Keterangan --}}
                                        <p class="fs-7 text-gray-700 mb-2">"{{ $data['verif_keterangan'] }}"</p>

                                        {{-- LOGIKA FOTO / DOKUMEN --}}
                                        @if (!empty($data['verif_file']))
                                            @php
                                                // URL File
                                                $verifUrl =
                                                    'https://apiaci-deliserdangsehat.deliserdangkab.go.id/storage/verifikasi_laporan/' .
                                                    $data['id'] .
                                                    '/' .
                                                    $data['verif_file'];

                                                // Cek Ekstensi
                                                $vExt = pathinfo($data['verif_file'], PATHINFO_EXTENSION);
                                                $vIsImg = in_array(strtolower($vExt), ['jpg', 'jpeg', 'png', 'webp']);
                                            @endphp

                                            @if ($vIsImg)
                                                {{-- TAMPILKAN GAMBAR LANGSUNG --}}
                                                <div class="mt-3 bg-white p-2 rounded border">
                                                    <a href="{{ $verifUrl }}" target="_blank" class="d-block">
                                                        <img src="{{ $verifUrl }}" class="rounded w-100"
                                                            style="height: 140px; object-fit: cover;"
                                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">

                                                        {{-- Fallback jika gambar error --}}
                                                        <div style="display:none;" class="text-center py-5">
                                                            <i class="ki-outline ki-picture text-muted fs-1"></i>
                                                            <span class="d-block fs-8 text-danger mt-1">Gagal memuat
                                                                preview</span>
                                                            <span class="btn btn-sm btn-light-primary mt-2 fs-9">Buka
                                                                Link</span>
                                                        </div>
                                                    </a>
                                                    <div class="text-center mt-1"><small class="text-muted"
                                                            style="font-size:10px;">Klik gambar untuk perbesar</small>
                                                    </div>
                                                </div>
                                            @else
                                                {{-- TAMPILKAN TOMBOL JIKA BUKAN GAMBAR (PDF/DOC) --}}
                                                <a href="{{ $verifUrl }}" target="_blank"
                                                    class="btn btn-sm btn-light-primary w-100 border border-primary border-dashed mt-2">
                                                    <i class="ki-outline ki-file fs-4"></i> Lihat Dokumen
                                                    ({{ strtoupper($vExt) }})
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- PANAH --}}
                            <div
                                class="flex-shrink-0 d-flex align-items-center justify-content-center h-100 me-4 pt-10">
                                <i class="ki-outline ki-arrow-right fs-2x text-gray-400"></i>
                            </div>
                        @endif

                        {{-- 4. PENANGANAN --}}
                        @if (!empty($data['penanganan_tgl']))
                            <div class="flex-shrink-0 me-4 w-300px">
                                <div class="card border border-dashed border-gray-400 bg-light-warning h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="badge badge-warning text-dark me-2">4</span>
                                            <span class="fw-bold text-gray-800">Sedang Dikerjakan</span>
                                        </div>
                                        <div class="text-muted fs-7 mb-2">
                                            <i class="ki-outline ki-calendar fs-6 me-1"></i>
                                            {{ \Carbon\Carbon::parse($data['penanganan_tgl'])->translatedFormat('d M Y, H:i') }}
                                        </div>
                                        <p class="fs-7 text-gray-700 mb-0">{{ $data['penanganan_keterangan'] }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- PANAH --}}
                            <div
                                class="flex-shrink-0 d-flex align-items-center justify-content-center h-100 me-4 pt-10">
                                <i class="ki-outline ki-arrow-right fs-2x text-gray-400"></i>
                            </div>
                        @endif

                        {{-- 5. SELESAI --}}
                        @if (!empty($data['selesai_tgl']))
                            <div class="flex-shrink-0 me-4 w-300px">
                                <div class="card border border-dashed border-gray-400 bg-light-success h-100">
                                    <div class="card-body p-4">
                                        {{-- Header Step --}}
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="badge badge-success me-2">5</span>
                                            <span class="fw-bold text-gray-800">Selesai</span>
                                        </div>
                                        {{-- Tanggal --}}
                                        <div class="text-muted fs-7 mb-2">
                                            <i class="ki-outline ki-calendar fs-6 me-1"></i>
                                            {{ \Carbon\Carbon::parse($data['selesai_tgl'])->translatedFormat('d M Y, H:i') }}
                                        </div>
                                        {{-- Keterangan --}}
                                        <p class="fs-7 text-gray-700 mb-2">{{ $data['selesai_keterangan'] }}</p>

                                        {{-- LOGIKA FOTO / DOKUMEN --}}
                                        @if (!empty($data['selesai_file']))
                                            @php
                                                // URL File Selesai
                                                $selesaiUrl =
                                                    'https://apiaci-deliserdangsehat.deliserdangkab.go.id/storage/selesai_laporan/' .
                                                    $data['id'] .
                                                    '/' .
                                                    $data['selesai_file'];

                                                // Cek Ekstensi
                                                $sExt = pathinfo($data['selesai_file'], PATHINFO_EXTENSION);
                                                $sIsImg = in_array(strtolower($sExt), ['jpg', 'jpeg', 'png', 'webp']);
                                            @endphp

                                            @if ($sIsImg)
                                                {{-- TAMPILKAN GAMBAR LANGSUNG --}}
                                                <div
                                                    class="mt-3 bg-white p-2 rounded border border-success border-dashed">
                                                    <a href="{{ $selesaiUrl }}" target="_blank" class="d-block">
                                                        <img src="{{ $selesaiUrl }}" class="rounded w-100"
                                                            style="height: 140px; object-fit: cover;"
                                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">

                                                        {{-- Fallback jika gambar error --}}
                                                        <div style="display:none;" class="text-center py-5">
                                                            <i class="ki-outline ki-picture text-muted fs-1"></i>
                                                            <span class="d-block fs-8 text-danger mt-1">Gagal memuat
                                                                preview</span>
                                                            <span class="btn btn-sm btn-light-success mt-2 fs-9">Buka
                                                                Link</span>
                                                        </div>
                                                    </a>
                                                    <div class="text-center mt-1"><small class="text-success"
                                                            style="font-size:10px;">Bukti Penyelesaian</small></div>
                                                </div>
                                            @else
                                                {{-- TAMPILKAN TOMBOL JIKA BUKAN GAMBAR --}}
                                                <a href="{{ $selesaiUrl }}" target="_blank"
                                                    class="btn btn-sm btn-light-success w-100 border border-success border-dashed mt-2">
                                                    <i class="ki-outline ki-check-circle fs-4"></i> Lihat Bukti
                                                    ({{ strtoupper($sExt) }})
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- 6. DITOLAK --}}
                        @if ($data['status_laporan'] == 5)
                            <div class="flex-shrink-0 me-4 w-300px">
                                <div class="card border border-dashed border-danger bg-light-danger h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="badge badge-danger me-2">X</span>
                                            <span class="fw-bold text-danger">Laporan Ditolak</span>
                                        </div>
                                        <p class="fs-7 text-gray-700 mb-0">
                                            <strong>Alasan:</strong><br>
                                            {{ $data['penerima_keterangan_tolak'] ?? ($data['verif_keterangan_tolak'] ?? ($data['penanganan_keterangan_tolak'] ?? ($data['selesai_keterangan_tolak'] ?? 'Tidak disebutkan'))) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Placeholder jika belum selesai (Indikator Proses Selanjutnya) --}}
                        @if ($data['status_laporan'] != 4 && $data['status_laporan'] != 5)
                            <div class="flex-shrink-0 w-200px opacity-50">
                                <div
                                    class="card border border-dashed border-gray-300 h-100 d-flex align-items-center justify-content-center p-5">
                                    <span class="text-gray-400 fw-bold">Menunggu Proses...</span>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
