<input type="hidden" name="hidden_id" id="hidden_id" value="{{ $data['id'] ?? '' }}" />

<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">Nama Kategori</label>
    <input type="text" name="nm_kategori" id="nm_kategori" class="form-control mb-3 mb-lg-0"
        placeholder="Contoh: Jalan Rusak" value="{{ $data['nm_kategori'] ?? '-' }}"/>
    <span class="text-danger error-text nm_kategori_error_edit"></span>
</div>