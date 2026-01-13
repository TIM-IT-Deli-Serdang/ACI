<style>
    .tracking-scroll::-webkit-scrollbar { height: 8px; }
    .tracking-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    .tracking-scroll::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
    .tracking-scroll::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
    .step-arrow { align-self: center; font-size: 1.5rem; color: #999; flex-shrink: 0; }
    .step-card { min-width: 280px; max-width: 280px; flex-shrink: 0; transition: all 0.2s; border: 1px solid #e4e6ef; }
    .step-card.active { border-color: #3E97FF; background-color: #f1faff; }
    .step-card.passed { border-color: #50cd89; background-color: #e8fff3; }
    .step-card.rejected { border-color: #f1416c; background-color: #fff5f8; }
</style>

@php
    $rawStatus = $data['status_raw'] ?? $data['status_laporan'] ?? 0;
    $s = (int) $rawStatus; 
    $isRejected = ($s == 5);
    
    // [CONFIG URL]
    // Kita siapkan 2 Versi URL untuk antisipasi struktur folder server
    $domain = 'https://apiaci-deliserdangsehat.deliserdangkab.go.id';
@endphp

<div class="d-flex flex-column gap-5">
    
    {{-- BAGIAN ATAS --}}
    <div class="row g-5">
        {{-- INFO & PETA --}}
        <div class="col-md-7">
            <div class="card shadow-sm border h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge badge-light-primary fw-bold fs-6 me-2">
                            @php $kats = [1=>'Jalan Rusak', 2=>'Drainase', 3=>'Banjir', 4=>'Tanggul/Jembatan', 5=>'Lainnya']; @endphp
                            {{ $kats[$data['kategori_laporan_id'] ?? 0] ?? 'Lainnya' }}
                        </span>
                        <span class="text-muted fs-7">#{{ $data['id'] ?? '-' }}</span>
                    </div>

                    <div class="bg-light rounded p-3 mb-4">
                        <label class="fw-bold text-gray-800 fs-7 mb-1">Deskripsi Masalah</label>
                        <div class="text-gray-700 fs-6 fst-italic">"{{ $data['deskripsi'] ?? '-' }}"</div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold text-gray-800 fs-7 mb-1">Alamat Lokasi</label>
                        <div class="text-gray-800">
                            {{ $data['alamat'] ?? '-' }}
                            @if(!empty($data['nama_kecamatan']))
                                <br><small class="text-muted">Kec. {{ $data['nama_kecamatan'] }}, Desa {{ $data['nama_desa'] ?? '-' }}</small>
                            @endif
                        </div>
                    </div>

                    @if(!empty($data['latitude']) && !empty($data['longitude']))
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $data['latitude'] }},{{ $data['longitude'] }}" target="_blank" class="btn btn-light-success w-100 btn-sm">
                            <i class="ki-outline ki-map fs-2"></i> Buka Google Maps
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- STATUS & FOTO UTAMA --}}
        <div class="col-md-5">
            <div class="card shadow-sm border h-100">
                <div class="card-body p-4 text-center">
                    <label class="fw-bold text-gray-800 fs-7 mb-3 d-block text-start">Foto Laporan</label>
                    
                    <div class="mb-4 bg-light rounded d-flex align-items-center justify-content-center position-relative" style="min-height: 200px;">
                        @if (!empty($data['file_masyarakat']))
                            @php
                                $fileRaw = $data['file_masyarakat'];
                                $idLaporan = $data['id'] ?? 'unknown';
                                $ext = pathinfo($fileRaw, PATHINFO_EXTENSION);
                                $isImg = in_array(strtolower($ext), ['jpg','jpeg','png','webp']);
                                
                                // URL 1: Pakai /storage/laporan/... (Standar Laravel)
                                $urlPrimary = $domain . '/storage/laporan/masyarakat/' . $idLaporan . '/' . $fileRaw;
                                
                                // URL 2: Langsung /laporan/... (Jika tanpa symlink storage)
                                $urlBackup = $domain . '/laporan/masyarakat/'. $idLaporan . '/' . $fileRaw;
                            @endphp
                            
                            @if($isImg)
                                {{-- 
                                    LOGIKA PENTING: 
                                    Coba load UrlPrimary. Jika gagal (onerror), otomatis ganti ke UrlBackup.
                                    Jika UrlBackup juga gagal, tampilkan tombol link manual.
                                --}}
                                <a href="{{ $urlPrimary }}" target="_blank" id="link_img_laporan">
                                    <img src="{{ $urlPrimary }}" 
                                         class="rounded mw-100" 
                                         style="max-height: 200px; object-fit: contain;" 
                                         onerror="
                                            this.onerror=null; 
                                            this.src='{{ $urlBackup }}'; 
                                            document.getElementById('link_img_laporan').href='{{ $urlBackup }}';
                                            // Jika backup juga gagal, sembunyikan gambar dan munculkan pesan error di bawah
                                            setTimeout(() => { if(!this.complete || this.naturalWidth === 0) { this.style.display='none'; document.getElementById('err_msg_laporan').style.display='block'; } }, 500);
                                         ">
                                </a>
                                
                                {{-- Pesan Error / Link Manual --}}
                                <div id="err_msg_laporan" style="display:none;" class="mt-3">
                                    <span class="text-danger fs-8 d-block mb-1">Gambar tidak tampil?</span>
                                    <a href="{{ $urlPrimary }}" target="_blank" class="btn btn-sm btn-light-primary fs-8">
                                        <i class="ki-outline ki-external-link fs-8"></i> Buka Link Gambar
                                    </a>
                                </div>
                            @else
                                <video src="{{ $urlPrimary }}" controls class="mw-100 rounded" style="max-height: 200px;"></video>
                            @endif
                        @else
                            <span class="text-muted">Tidak ada foto</span>
                        @endif
                    </div>

                    <div class="text-start">
                        <label class="fw-bold text-gray-800 fs-7 mb-1">Status Saat Ini</label>
                        <div>
                            @if($s==0) <span class="badge badge-lg badge-secondary w-100 py-3 fs-6">Pengajuan Masuk</span>
                            @elseif($s==1) <span class="badge badge-lg badge-success w-100 py-3 fs-6">Diterima</span>
                            @elseif($s==2) <span class="badge badge-lg badge-primary w-100 py-3 fs-6">Verifikasi</span>
                            @elseif($s==3) <span class="badge badge-lg badge-warning w-100 py-3 fs-6">Pengerjaan</span>
                            @elseif($s==4) <span class="badge badge-lg badge-dark w-100 py-3 fs-6">Selesai</span>
                            @elseif($s==5) <span class="badge badge-lg badge-danger w-100 py-3 fs-6">Ditolak</span>
                            @endif
                        </div>
                        <div class="mt-2 text-muted fs-7">
                            Dilaporkan: {{ \Carbon\Carbon::parse($data['created_at'])->format('d M Y, H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TRACKING --}}
    <div class="card shadow-sm border">
        <div class="card-header min-h-auto py-3">
            <h5 class="card-title fw-bold text-gray-800 m-0">
                <i class="ki-outline ki-route fs-2 me-2 text-primary"></i> Tracking Laporan
            </h5>
        </div>
        <div class="card-body py-4">
            <div class="d-flex flex-nowrap overflow-auto py-2 px-1 gap-3 tracking-scroll">
                
                {{-- STEP 1 --}}
                <div class="card step-card shadow-sm p-4 {{ $s >= 0 && !$isRejected ? 'passed' : '' }}">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge badge-circle badge-primary me-2">1</span>
                        <span class="fw-bold fs-7">Laporan Dibuat</span>
                    </div>
                    <div class="text-muted fs-8 mb-2">{{ \Carbon\Carbon::parse($data['created_at'])->format('d M Y, H:i') }}</div>
                    <div class="fs-7 text-gray-600">Laporan dikirim pelapor.</div>
                </div>

                @if($isRejected)
                    <i class="ki-outline ki-arrow-right step-arrow"></i>
                    <div class="card step-card rejected shadow-sm p-4">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge badge-circle badge-danger me-2"><i class="ki-outline ki-cross text-white fs-8"></i></span>
                            <span class="fw-bold fs-7 text-danger">Ditolak</span>
                        </div>
                        <div class="fs-7 bg-white rounded border border-danger border-dashed p-2">
                            {{ $data['penerima_keterangan_tolak'] ?? $data['verif_keterangan_tolak'] ?? '-' }}
                        </div>
                    </div>
                @else
                    {{-- STEP 2 --}}
                    <i class="ki-outline ki-arrow-right step-arrow"></i>
                    <div class="card step-card shadow-sm p-4 {{ $s >= 1 ? 'passed' : '' }} {{ $s == 1 ? 'active' : '' }}">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge badge-circle {{ $s >= 1 ? 'badge-success' : 'badge-light' }} me-2">2</span>
                            <span class="fw-bold fs-7">Diterima Admin</span>
                        </div>
                        @if($s >= 1)
                            <div class="fs-7 text-gray-700 mb-2">"{{ $data['penerima_keterangan'] ?? 'Diterima' }}"</div>
                            @if(!empty($data['upt_id'])) <div class="badge badge-light-primary fs-8">Disposisi UPT</div> @endif
                        @else <div class="fs-7 text-gray-400">Menunggu...</div> @endif
                    </div>

                    {{-- STEP 3 (FOTO VERIFIKASI) --}}
                    <i class="ki-outline ki-arrow-right step-arrow"></i>
                    <div class="card step-card shadow-sm p-4 {{ $s >= 2 ? 'passed' : '' }} {{ $s == 2 ? 'active' : '' }}">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge badge-circle {{ $s >= 2 ? 'badge-primary' : 'badge-light' }} me-2">3</span>
                            <span class="fw-bold fs-7">Verifikasi UPT</span>
                        </div>
                        @if($s >= 2)
                            <div class="fs-7 text-gray-700 mb-2">"{{ $data['verif_keterangan'] ?? '-' }}"</div>
                            @if(!empty($data['verif_file']))
                                @php
                                    $vRaw = $data['verif_file'];
                                    $idLaporan = $data['id'] ?? 'unknown';
                                    $vUrl1 = $domain . '/storage/verifikasi_laporan/' . $idLaporan . '/' . $vRaw;
                                    $vUrl2 = $domain . '/laporan/verifikasi_laporan/' .$idLaporan . '/' . $vRaw; 
                                    $vExt = pathinfo($vRaw, PATHINFO_EXTENSION);
                                @endphp
                                @if(in_array(strtolower($vExt), ['jpg','jpeg','png','webp']))
                                    <a href="{{ $vUrl1 }}" target="_blank" id="link_v">
                                        <img src="{{ $vUrl1 }}" class="rounded w-100 border" style="height: 100px; object-fit: cover;" 
                                             onerror="this.onerror=null; this.src='{{ $vUrl2 }}'; document.getElementById('link_v').href='{{ $vUrl2 }}';">
                                    </a>
                                @else <a href="{{ $vUrl1 }}" target="_blank" class="btn btn-sm btn-light-primary w-100">Dokumen</a> @endif
                            @endif
                        @else <div class="fs-7 text-gray-400">Menunggu...</div> @endif
                    </div>

                    {{-- STEP 4 --}}
                    <i class="ki-outline ki-arrow-right step-arrow"></i>
                    <div class="card step-card shadow-sm p-4 {{ $s >= 3 ? 'passed' : '' }} {{ $s == 3 ? 'active' : '' }}">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge badge-circle {{ $s >= 3 ? 'badge-warning' : 'badge-light' }} me-2">4</span>
                            <span class="fw-bold fs-7">Pengerjaan</span>
                        </div>
                        @if($s >= 3)
                            <div class="fs-7 text-gray-700">"{{ $data['penanganan_keterangan'] ?? '-' }}"</div>
                        @else <div class="fs-7 text-gray-400">Menunggu...</div> @endif
                    </div>

                    {{-- STEP 5 (FOTO SELESAI) --}}
                    <i class="ki-outline ki-arrow-right step-arrow"></i>
                    <div class="card step-card shadow-sm p-4 {{ $s == 4 ? 'passed bg-light-success border-success' : '' }}">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge badge-circle {{ $s == 4 ? 'badge-success' : 'badge-light' }} me-2">5</span>
                            <span class="fw-bold fs-7">Selesai</span>
                        </div>
                        @if($s == 4)
                            <div class="fs-7 text-gray-700 mb-2">"{{ $data['selesai_keterangan'] ?? '-' }}"</div>
                            @if(!empty($data['selesai_file']))
                                @php
                                    $dRaw = $data['selesai_file'];
                                    $idSelesai = $data['id'] ?? 'unknown';
                                    $dUrl1 = $domain . '/storage/selesai_laporan/' . $idSelesai . '/' . $dRaw;
                                    $dUrl2 = $domain . '/laporan/selesai_laporan/' .$idSelesai . '/' . $dRaw;
                                    $dExt = pathinfo($dRaw, PATHINFO_EXTENSION);
                                @endphp
                                @if(in_array(strtolower($dExt), ['jpg','jpeg','png','webp']))
                                    <a href="{{ $dUrl1 }}" target="_blank" id="link_d">
                                        <img src="{{ $dUrl1 }}" class="rounded w-100 border" style="height: 100px; object-fit: cover;" 
                                             onerror="this.onerror=null; this.src='{{ $dUrl2 }}'; document.getElementById('link_d').href='{{ $dUrl2 }}';">
                                    </a>
                                @else <a href="{{ $dUrl1 }}" target="_blank" class="btn btn-sm btn-light-success w-100">Dokumen</a> @endif
                            @endif
                        @else <div class="fs-7 text-gray-400">Belum selesai...</div> @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>