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

{{-- ALAMAT --}}
<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">Alamat Lengkap</label>
    <textarea name="alamat" class="form-control form-control-solid" rows="2" placeholder="Nama Jalan, Desa, Kecamatan...">{{ $data['alamat'] ?? '' }}</textarea>
</div>

{{-- LOKASI LAT/LONG --}}
<div class="row mb-7">
    <div class="col-md-6 fv-row">
        <label class="fw-semibold fs-7 mb-2">Latitude</label>
        <input type="text" name="latitude" class="form-control form-control-solid" placeholder="-3.xxxx" value="{{ $data['latitude'] ?? '' }}" />
    </div>
    <div class="col-md-6 fv-row">
        <label class="fw-semibold fs-7 mb-2">Longitude</label>
        <input type="text" name="longitude" class="form-control form-control-solid" placeholder="98.xxxx" value="{{ $data['longitude'] ?? '' }}" />
    </div>
</div>

{{-- HIDDEN WILAYAH (Sesuai logic store) --}}
<input type="hidden" name="kecamatan_id" value="{{ $data['kecamatan_id'] ?? 1 }}">
<input type="hidden" name="kelurahan_id" value="{{ $data['kelurahan_id'] ?? 1 }}">

{{-- FOTO BUKTI --}}
<div class="fv-row mb-7">
    <label class="fw-semibold fs-7 mb-2">Foto Bukti</label>
    
    {{-- Tampilkan foto lama jika ada --}}
    @if (!empty($data['file_masyarakat']))
        <div class="mb-3">
            <div class="symbol symbol-100px symbol-2by3 me-4">
                <div class="symbol-label" style="background-image: url('http://10.0.22.97/storage/laporan/masyarakat/{{ $data['file_masyarakat'] }}')"></div>
            </div>
            <div class="text-muted fs-7 mt-1">Foto saat ini. Upload baru untuk mengganti.</div>
        </div>
    @endif

    <input type="file" name="file_masyarakat" class="form-control form-control-solid" accept=".png, .jpg, .jpeg">
    <div class="text-muted fs-7 mt-2">Biarkan kosong jika tidak ingin mengubah foto.</div>
</div>

{{-- RE-INIT SELECT2 PADA AJAX LOAD --}}
<script>
    $(document).ready(function() {
        // Karena form ini diload via AJAX, kita perlu re-init component select2 manual
        $('#FormEditModalID select[data-control="select2"]').select2({
            dropdownParent: $('#Modal_Edit_Data'),
            minimumResultsForSearch: Infinity
        });
    });
</script>