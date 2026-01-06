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

{{-- [BARU] WILAYAH DELI SERDANG (EDIT) --}}
<div class="row mb-7">
    <div class="col-md-6 fv-row">
        <label class="required fs-6 fw-semibold mb-2">Kecamatan</label>
        {{-- Simpan nilai lama di data-old --}}
        <select id="edit_kecamatan" name="kecamatan_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Kecamatan" data-old="{{ $data['kecamatan_id'] ?? '' }}">
            <option></option>
        </select>
    </div>
    <div class="col-md-6 fv-row">
        <label class="required fs-6 fw-semibold mb-2">Kelurahan/Desa</label>
        <select id="edit_kelurahan" name="kelurahan_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Kelurahan" data-old="{{ $data['kelurahan_id'] ?? '' }}">
            <option></option>
        </select>
    </div>
</div>

{{-- ALAMAT --}}
<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">Alamat Lengkap</label>
    <textarea name="alamat" class="form-control form-control-solid" rows="2" placeholder="Nama Jalan, Desa, Kecamatan...">{{ $data['alamat'] ?? '' }}</textarea>
</div>

{{-- [BARU] LOKASI LAT/LONG (EDIT) --}}
<div class="row mb-7">
    <div class="col-md-5 fv-row">
        <label class="fw-semibold fs-7 mb-2">Latitude</label>
        <input type="text" id="edit_lat" name="latitude" class="form-control form-control-solid" placeholder="-3.xxxx" value="{{ $data['latitude'] ?? '' }}" />
    </div>
    <div class="col-md-5 fv-row">
        <label class="fw-semibold fs-7 mb-2">Longitude</label>
        <input type="text" id="edit_long" name="longitude" class="form-control form-control-solid" placeholder="98.xxxx" value="{{ $data['longitude'] ?? '' }}" />
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
            $fileUrl = 'http://10.0.22.97/storage/laporan/masyarakat/' . $data['file_masyarakat'];
            $ext = pathinfo($data['file_masyarakat'], PATHINFO_EXTENSION);
            $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            $isVideo = in_array(strtolower($ext), ['mp4', 'mov', 'avi', 'mkv', 'webm']);
        @endphp

        <div class="mb-3 border rounded p-2 bg-light text-center">
            @if($isImage)
                <div class="symbol symbol-100px symbol-2by3 me-4">
                    <div class="symbol-label" style="background-image: url('{{ $fileUrl }}')"></div>
                </div>
                <div class="text-muted fs-7 mt-1">Foto saat ini.</div>
            @elseif($isVideo)
                <video src="{{ $fileUrl }}" controls class="mw-100 rounded" style="max-height: 150px;"></video>
                <div class="text-muted fs-7 mt-1">Video saat ini.</div>
            @else
                <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-light-primary">
                    <i class="ki-outline ki-file fs-2"></i> Lihat File Saat Ini
                </a>
            @endif
        </div>
    @endif

    <input type="file" name="file_masyarakat" class="form-control form-control-solid" 
           accept=".png, .jpg, .jpeg, .mp4, .mov, .avi, .mkv, .webm">
    <div class="text-muted fs-7 mt-2">
        Biarkan kosong jika tidak ingin mengubah file.<br>
        Max: Foto 2MB, Video 120MB.
    </div>
</div>

<script>
    $(document).ready(function() {
        // Re-Init Select2
        $('#FormEditModalID select[data-control="select2"]').select2({
            dropdownParent: $('#Modal_Edit_Data'),
            minimumResultsForSearch: Infinity
        });

        // === LOGIKA WILAYAH EDIT (DELI SERDANG ID 1207) ===
        const KAB_ID = '1207';
        // Pastikan controller mengirim ID, bukan nama
        const oldKec = $('#edit_kecamatan').data('old'); 
        const oldKel = $('#edit_kelurahan').data('old');

        // 1. Load Kecamatan
        $.getJSON(`https://ibnux.github.io/data-indonesia/kecamatan/${KAB_ID}.json`, function(res) {
            $('#edit_kecamatan').empty().append('<option value="">-- Pilih Kecamatan --</option>');
            res.forEach(kec => {
                // [PERBAIKAN] Bandingkan ID dengan ID
                // Pastikan oldKec dan kec.id tipe datanya sama (string/int) pakai ==
                const isSelected = (kec.id == oldKec) ? 'selected' : '';
                
                // Set value ke ID
                $('#edit_kecamatan').append(`<option data-id="${kec.id}" value="${kec.id}" ${isSelected}>${kec.nama}</option>`);
            });

            // Jika ada oldKec (ID), trigger load kelurahan
            if(oldKec) {
                loadKelurahan(oldKec, oldKel);
            }
        });

        // 2. Fungsi Load Kelurahan
        function loadKelurahan(kecId, selectedKel = null) {
            $('#edit_kelurahan').empty().append('<option value="">Loading...</option>');
            $.getJSON(`https://ibnux.github.io/data-indonesia/kelurahan/${kecId}.json`, function(res) {
                $('#edit_kelurahan').empty().append('<option value="">-- Pilih Kelurahan --</option>');
                res.forEach(kel => {
                    // [PERBAIKAN] Bandingkan ID dengan ID
                    const isSelected = (kel.id == selectedKel) ? 'selected' : '';
                    // Set value ke ID
                    $('#edit_kelurahan').append(`<option value="${kel.id}" ${isSelected}>${kel.nama}</option>`);
                });
            });
        }

        // 3. Event Change Kecamatan
        $('#edit_kecamatan').on('change', function() {
            // Ambil value langsung karena value sudah ID
            const kecId = $(this).val();
            if(kecId) loadKelurahan(kecId);
            else $('#edit_kelurahan').empty().append('<option value="">-- Pilih Kelurahan --</option>');
        });

        // === LOGIKA AUTO LOCATION EDIT ===
        $('#btn-get-loc-edit').click(function() {
            var btn = $(this);
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        $('#edit_lat').val(position.coords.latitude);
                        $('#edit_long').val(position.coords.longitude);
                        Swal.fire("Lokasi Diperbarui", "", "success");
                    },
                    function(error) {
                        Swal.fire("Gagal", "Pastikan GPS aktif.", "error");
                    }
                );
            }
        });
    });
</script>