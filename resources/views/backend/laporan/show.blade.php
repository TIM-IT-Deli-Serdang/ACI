<div class="row">
    <div class="col-md-7">
        <div class="fv-row mb-7">
            <label class="fw-semibold fs-7 mb-2">Kategori Laporan</label>
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
            <input type="text" class="form-control form-control-solid" value="{{ $kategori }}" readonly />
        </div>

        <div class="fv-row mb-7">
            <label class="fw-semibold fs-7 mb-2">Deskripsi Masalah</label>
            <textarea class="form-control form-control-solid" rows="4" readonly>{{ $data['deskripsi'] ?? '-' }}</textarea>
        </div>

        <div class="fv-row mb-7">
            <label class="fw-semibold fs-7 mb-2">Alamat Lokasi</label>
            <textarea class="form-control form-control-solid" rows="2" readonly>{{ $data['alamat'] ?? '-' }}</textarea>
        </div>

        <div class="row">
            <div class="col-6 mb-7">
                <label class="fw-semibold fs-7 mb-2">Latitude</label>
                <input type="text" class="form-control form-control-solid"
                    value="{{ $data['latitude'] ?? '-' }}" readonly />
            </div>

            <div class="col-6 mb-7">
                <label class="fw-semibold fs-7 mb-2">Longitude</label>
                <div class="input-group">
                    <input type="text" class="form-control form-control-solid"
                        value="{{ $data['longitude'] ?? '-' }}" readonly />
                    
                    @if (!empty($data['latitude']) && !empty($data['longitude']))
                        <a href="https://www.google.com/maps?q={{ $data['latitude'] }},{{ $data['longitude'] }}"
                            target="_blank" class="btn btn-light-primary btn-icon" title="Lihat Lokasi di Google Maps">
                            <i class="ki-outline ki-map fs-2"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        </div>

    <div class="col-md-5">
        @php
            // Deklarasi Base URL Storage Manual
            $storageBaseUrl = 'http://10.0.22.97/storage/laporan/masyarakat/';
        @endphp

        <div class="fv-row mb-7">
            <label class="fw-semibold fs-7 mb-2">Foto Bukti</label>
            <div class="border rounded p-2 text-center bg-light">
                @if (!empty($data['file_masyarakat']))
                    <a href="{{ $storageBaseUrl . $data['file_masyarakat'] }}" target="_blank">
                        <img src="{{ $storageBaseUrl . $data['file_masyarakat'] }}" class="img-fluid rounded shadow-sm"
                            style="max-height: 250px; width: 100%; object-fit: cover;" alt="Foto Laporan">
                    </a>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center py-10 text-muted">
                        <i class="ki-outline ki-picture fs-3x mb-2"></i>
                        <span>Tidak ada foto</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="fv-row mb-7">
            <label class="fw-semibold fs-7 mb-2">Status Saat Ini</label>
            <div>
                @switch($data['status_laporan'] ?? 0)
                    @case(1)
                        <span class="badge badge-warning badge-lg p-3">Pending (Menunggu Verifikasi)</span>
                    @break

                    @case(2)
                        <span class="badge badge-success badge-lg p-3">Disetujui / Sedang Proses</span>
                    @break

                    @case(3)
                        <span class="badge badge-danger badge-lg p-3">Ditolak</span>
                    @break

                    @default
                        <span class="badge badge-secondary badge-lg p-3">Draft</span>
                @endswitch
            </div>
        </div>

        <div class="fv-row mb-7">
            <label class="fw-semibold fs-7 mb-2">Tanggal Dilaporkan</label>
            <input type="text" class="form-control form-control-solid"
                value="{{ \Carbon\Carbon::parse($data['created_at'])->translatedFormat('d F Y, H:i') }} WIB" readonly />
        </div>
    </div>
</div>