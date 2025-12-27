<input type="hidden" name="hidden_id" id="hidden_id" value="{{ $data['id'] ?? '' }}" />

<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">Nama UPT</label>
    <input type="text" name="nama_upt" id="nama_upt" class="form-control mb-3 mb-lg-0"
        placeholder="Contoh: UPT Wilayah 1" value="{{ $data['nama_upt'] ?? '-' }}"/>
    <span class="text-danger error-text nama_upt_error_edit"></span>
</div>