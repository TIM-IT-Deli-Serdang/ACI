<div class="row">
<div class="fv-row mb-5 col-8">
    <label class="required fw-semibold fs-7 mb-2">Nama User</label>
    <input type="text" class="form-control form-control-solid border border-gray-300 mb-3 mb-lg-5"
         value="{{ $data['name'] ?? '-' }}" readonly/>
</div>
<div class="fv-row mb-5 col-4">
    <label class="required fw-semibold fs-7 mb-2">Role</label>
    <input type="text" class="form-control form-control-solid border border-gray-300 mb-3 mb-lg-5"
         value="{{ $data['roles'][0]['name'] ?? '-' }}" readonly/>
</div>
</div>
<div class="row">
    <div class="fv-row mb-5 col-6">
        <label class="required fw-semibold fs-7 mb-2">NIK</label>
        <input type="text" class="form-control form-control-solid border border-gray-300"
             value="{{ $data['nik'] ?? '-' }}" readonly/>
    </div>

    <div class="fv-row mb-5 col-6">
        <label class="required fw-semibold fs-7 mb-2">Unit UPT</label>
        <input type="text" class="form-control form-control-solid border border-gray-300"
             value="{{ $data['upt']['nama_upt'] ?? '-' }}" readonly/>
    </div>
</div>




                        <div class="row">
                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-5 col-6">
                                <label class="required fw-semibold fs-7 mb-2">Email</label>
                                <input type="text" name="email" id="email" class="form-control form-control-solid border border-gray-300 mb-3 mb-lg-0"
                                    value="{{ $data['email'] ?? '-' }}" readonly />
                            </div>

                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-5 col-6">
                                <label class="required fw-semibold fs-7 mb-2">No. WhatsApp</label>
                                <input type="text" name="no_wa" id="no_wa" class="form-control form-control-solid border border-gray-300 mb-3 mb-lg-0"
                                    value="{{ $data['no_wa'] ?? '-' }}" readonly />
                            </div>
                        </div>


                      
                       
@php use Carbon\Carbon; @endphp

<div class="row">
    <div class="fv-row mb-5 col-6">
        <label class="required fw-semibold fs-7 mb-2">Last Login</label>
        <input type="text"
               class="form-control form-control-sm form-control-solid border border-gray-300 mb-3 mb-lg-5"
               value="{{ isset($data['last_login_at']) 
                        ? Carbon::parse($data['last_login_at'])
                            ->locale('id')
                            ->timezone('Asia/Jakarta')
                            ->translatedFormat('d F Y H.i')
                        : '-' }}"
               readonly />
    </div>

    <div class="fv-row mb-5 col-6">
        <label class="required fw-semibold fs-7 mb-2">Last IP Address</label>
        <input type="text"
               class="form-control form-control-sm form-control-solid border border-gray-300 mb-3 mb-lg-5"
               value="{{ isset($data['last_login_ip']) 
                        ? $data['last_login_ip']
                        : '-' }}"
               readonly />
    </div>

    <div class="fv-row mb-5 col-6">
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

    <div class="fv-row mb-5 col-6">
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
