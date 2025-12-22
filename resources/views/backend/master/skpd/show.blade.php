<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">Kode SKPD</label>
    <input type="text" class="form-control form-control-solid border border-gray-300 mb-3 mb-lg-5"
         value="{{ $data['kode_skpd'] ?? '-' }}" readonly/>
</div>

<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">Nama SKPD</label>
    <input type="text" class="form-control form-control-solid border border-gray-300 mb-3 mb-lg-5"
         value="{{ $data['nama_skpd'] ?? '-' }}" readonly/>
</div>



                        <div class="row">
                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">Kepala SKPD</label>
                                <input type="text" name="kepala_skpd" id="kepala_skpd" class="form-control form-control-solid border border-gray-300 mb-3 mb-lg-0"
                                    value="{{ $data['kepala_skpd'] ?? '-' }}" readonly />
                            </div>

                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">NIP Kepala SKPD</label>
                                <input type="text" name="nip_kepala" id="nip_kepala" class="form-control form-control-solid border border-gray-300 mb-3 mb-lg-0"
                                    value="{{ $data['nip_kepala'] ?? '-' }}" readonly />
                            </div>
                        </div>


                        <div class="row">
                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">latitude</label>
                                <input type="text" name="latitude" id="latitude" class="form-control form-control-solid border border-gray-300 mb-3 mb-lg-0"
                                    value="{{ $data['latitude'] ?? '-' }}" readonly />
                            </div>

                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">longitude</label>
                                <input type="text" name="longitude" id="longitude" class="form-control form-control-solid border border-gray-300 mb-3 mb-lg-0"
                                    value="{{ $data['longitude'] ?? '-' }}" readonly />
                            </div>
                        </div>

                       
@php use Carbon\Carbon; @endphp

<div class="row">
    <div class="fv-row mb-7 col-6">
        <label class="required fw-semibold fs-7 mb-2">Created At</label>
        <input type="text"
               class="form-control form-control-sm form-control-solid border border-gray-300 mb-3 mb-lg-5"
               value="{{ isset($data['created_at']) 
                        ? Carbon::parse($data['created_at'])
                            ->locale('id')
                            ->timezone('Asia/Jakarta')
                            ->translatedFormat('d F Y H.i')
                        : '-' }}"
               readonly />
    </div>

    <div class="fv-row mb-7 col-6">
        <label class="required fw-semibold fs-7 mb-2">Updated At</label>
        <input type="text"
               class="form-control form-control-sm form-control-solid border border-gray-300 mb-3 mb-lg-5"
               value="{{ isset($data['updated_at']) 
                        ? Carbon::parse($data['updated_at'])
                            ->locale('id')
                            ->timezone('Asia/Jakarta')
                            ->translatedFormat('d F Y H.i')
                        : '-' }}"
               readonly />
    </div>
</div>

