@php use Carbon\Carbon; @endphp

<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">Nama UPT</label>
    <input type="text" class="form-control form-control-solid border border-gray-300"
         value="{{ $data['nama_upt'] ?? '-' }}" readonly/>
</div>

<div class="row">
    <div class="fv-row mb-7 col-6">
        <label class="fw-semibold fs-7 mb-2">Dibuat Pada</label>
        <input type="text"
               class="form-control form-control-sm form-control-solid border border-gray-300"
               value="{{ isset($data['created_at']) 
                        ? Carbon::parse($data['created_at'])->locale('id')->timezone('Asia/Jakarta')->translatedFormat('d F Y H.i')
                        : '-' }}"
               readonly />
    </div>

    <div class="fv-row mb-7 col-6">
        <label class="fw-semibold fs-7 mb-2">Diupdate Pada</label>
        <input type="text"
               class="form-control form-control-sm form-control-solid border border-gray-300"
               value="{{ isset($data['updated_at']) 
                        ? Carbon::parse($data['updated_at'])->locale('id')->timezone('Asia/Jakarta')->translatedFormat('d F Y H.i')
                        : '-' }}"
               readonly />
    </div>
</div>