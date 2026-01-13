
@php
    // Konfigurasi Domain API
    $apiDomain = 'https://apiaci-deliserdangsehat.deliserdangkab.go.id';
@endphp
<input type="hidden" name="hidden_id" id="hidden_id" value="{{ $data['id'] ?? '' }}" />

{{-- KATEGORI LAPORAN --}}
<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">Kategori Laporan</label>
    <select name="kategori_laporan_id" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Pilih Kategori">
        <option></option>
        @php $kat = $data['kategori_laporan_id'] ?? 0; @endphp
        <option value="1" {{ $kat == 1 ? 'selected' : '' }}>Jalan Rusak</option>
        <option value="2" {{ $kat == 2 ? 'selected' : '' }}>Drainase Tersumbat</option>
        <option value="3" {{ $kat == 3 ? 'selected' : '' }}>Banjir</option>
        <option value="4" {{ $kat == 4 ? 'selected' : '' }}>Tanggul / Jembatan Rusak</option>
        <option value="5" {{ $kat == 5 ? 'selected' : '' }}>Infrastruktur Lainnya</option>
    </select>
</div>

{{-- DESKRIPSI --}}
<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">Deskripsi Masalah</label>
    <textarea name="deskripsi" class="form-control form-control-solid" rows="3" placeholder="Jelaskan detail kerusakan...">{{ $data['deskripsi'] ?? '' }}</textarea>
</div>

{{-- WILAYAH (API DEPENDENT DROPDOWN) --}}
<div class="row mb-7">
    {{-- KECAMATAN --}}
    <div class="col-md-6 fv-row">
        <label class="required fw-semibold fs-7 mb-2">Kecamatan</label>
        {{-- Kita simpan ID lama di data-selected agar JS bisa membacanya --}}
        <select
            name="wilayah_kecamatan_id"
            id="kecamatanEdit"
            class="form-select form-select-solid"
            data-control="select2"
            data-placeholder="Pilih Kecamatan"
            data-dropdown-parent="#Modal_Edit_Data"
            data-selected="{{ $data['wilayah_kecamatan_id'] ?? '' }}"
            required>
            <option value="">Memuat...</option>
        </select>
    </div>

    {{-- KELURAHAN / DESA --}}
    <div class="col-md-6 fv-row">
        <label class="required fw-semibold fs-7 mb-2">Desa / Kelurahan</label>
        {{-- Kita simpan ID lama di data-selected agar JS bisa membacanya --}}
        <select
            name="wilayah_kelurahan_id"
            id="kelurahanEdit"
            class="form-select form-select-solid"
            data-control="select2"
            data-placeholder="Pilih Desa"
            data-dropdown-parent="#Modal_Edit_Data"
            data-selected="{{ $data['wilayah_kelurahan_id'] ?? '' }}"
            required>
            <option value="">-- Pilih Kecamatan Dulu --</option>
        </select>
    </div>
</div>


{{-- ALAMAT --}}
<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">Alamat Lengkap</label>
    <textarea name="alamat" class="form-control form-control-solid" rows="2" placeholder="Nama Jalan, Dusun, Patokan...">{{ $data['alamat'] ?? '' }}</textarea>
</div>

{{-- LOKASI LAT/LONG --}}
<div class="row mb-7">
    <div class="col-md-5 fv-row">
        <label class="fw-semibold fs-7 mb-2">Latitude</label>
        <input type="text" id="edit_lat" name="latitude" class="form-control form-control-solid" placeholder="-3.xxxx" value="{{ $data['latitude'] ?? '' }}" readonly />
    </div>
    <div class="col-md-5 fv-row">
        <label class="fw-semibold fs-7 mb-2">Longitude</label>
        <input type="text" id="edit_long" name="longitude" class="form-control form-control-solid" placeholder="98.xxxx" value="{{ $data['longitude'] ?? '' }}" readonly />
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="button" id="btn-get-loc-edit" class="btn btn-icon btn-light-primary w-100" title="Ambil Lokasi Saya">
            <i class="ki-outline ki-geolocation fs-1"></i>
        </button>
    </div>
</div>

{{-- FOTO / VIDEO BUKTI --}}
<div class="fv-row mb-7">
    <label class="fw-semibold fs-7 mb-2">Foto / Video Bukti</label>
    
    @if (!empty($data['file_masyarakat']))
            @php
                $fileName = $data['file_masyarakat'];
                $fileId = $data['id'] ?? 'unknown';
                // URL Utama (Storage)
                $urlStorage = $apiDomain . '/storage/laporan/masyarakat/'. $fileId . '/' . $fileName;
                // URL Cadangan (Public)
                $urlPublic  = $apiDomain . '/laporan/masyarakat/'. $fileId . '/' . $fileName;

                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            @endphp

            <div class="mb-3 border rounded p-3 bg-light text-center position-relative">
                @if($isImage)
                    {{-- Container Gambar --}}
                    <div id="img-container-{{ $data['id'] }}" class="d-block">
                        <img src="{{ $urlStorage }}" 
                             class="mw-100 rounded border shadow-sm" 
                             style="max-height: 150px; object-fit: contain;"
                             onerror="
                                // Jika gagal load dari storage, coba public
                                if (this.src != '{{ $urlPublic }}') {
                                    this.src = '{{ $urlPublic }}';
                                } else {
                                    // Jika public juga gagal, sembunyikan gambar dan tampilkan pesan error
                                    this.style.display = 'none';
                                    document.getElementById('error-msg-{{ $data['id'] }}').style.display = 'block';
                                }
                             ">
                    </div>

                    {{-- Pesan Error (Hidden by default) --}}
                    <div id="error-msg-{{ $data['id'] }}" style="display:none;" class="py-3">
                        <i class="ki-outline ki-file-deleted fs-1 text-danger mb-2"></i>
                        <div class="text-danger fw-bold fs-7">File fisik tidak ditemukan di server.</div>
                        <div class="text-muted fs-8">Nama file: {{ $fileName }}</div>
                        <div class="mt-2 text-primary fs-8 fst-italic">Silakan upload ulang gambar baru di bawah.</div>
                    </div>

                @else
                    {{-- Untuk Video / File Lain --}}
                    <a href="{{ $urlStorage }}" target="_blank" class="btn btn-sm btn-light-primary">
                        <i class="ki-outline ki-file-down fs-2"></i> Cek File ({{ $ext }})
                    </a>
                @endif
            </div>
        @endif

    <input type="file" name="file_masyarakat" class="form-control form-control-solid" 
           accept="image/,video/">
    <div class="text-muted fs-7 mt-2">
        Biarkan kosong jika tidak ingin mengubah file.<br>
        Max: Foto 2MB, Video 120MB.
    </div>
</div>

<script>
    $(document).ready(function() {
        // 1. Re-Init Select2 (Wajib karena element baru masuk DOM)
        // Kita init spesifik agar tidak bentrok
        $('#kecamatanEdit, #kelurahanEdit').select2({
            dropdownParent: $('#Modal_Edit_Data'),
            width: '100%'
        });

        $('#FormEditModalID select[name="kategori_laporan_id"]').select2({
            dropdownParent: $('#Modal_Edit_Data'),
            minimumResultsForSearch: Infinity
        });

        // 2. LOGIKA LOAD DATA API (Menggunakan Fungsi Global dari Index)
        // Ambil data-selected yang kita taruh di HTML tadi
        const selectedKecId = $('#kecamatanEdit').data('selected');
        const selectedKelId = $('#kelurahanEdit').data('selected');

        // A. Panggil API Kecamatan
        // Fungsi loadKecamatan(selector, selectedId) ada di index.blade.php
        if (typeof loadKecamatan === "function") {
            loadKecamatan('#kecamatanEdit', selectedKecId);
        } else {
            console.error("Fungsi loadKecamatan belum dimuat di parent page.");
        }

        // B. Jika sudah ada kecamatan terpilih, Panggil API Desa
        if (selectedKecId && typeof loadKelurahan === "function") {
            loadKelurahan(selectedKecId, '#kelurahanEdit', selectedKelId);
        }

        // C. Event Listener: Jika user mengubah Kecamatan di form Edit
        $('#kecamatanEdit').on('change', function() {
            const newKecId = $(this).val();
            if (typeof loadKelurahan === "function") {
                loadKelurahan(newKecId, '#kelurahanEdit');
            }
        });

        // 3. LOGIKA GPS EDIT
        $('#btn-get-loc-edit').click(function() {
            var btn = $(this);
            if (navigator.geolocation) {
                btn.addClass('spinner spinner-primary spinner-center');
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        $('#edit_lat').val(position.coords.latitude);
                        $('#edit_long').val(position.coords.longitude);
                        btn.removeClass('spinner spinner-primary spinner-center');
                        Swal.fire("Lokasi Diperbarui", "", "success");
                    },
                    function(error) {
                        btn.removeClass('spinner spinner-primary spinner-center');
                        Swal.fire("Gagal", "Pastikan GPS aktif.", "error");
                    }
                );
            }
        });
    });
</script>