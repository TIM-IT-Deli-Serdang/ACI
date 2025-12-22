<div class="fv-row mb-5">
    <label class="required fw-semibold fs-7 mb-2">Role Name</label>
    <input type="text" class="form-control form-control-solid border border-gray-300 mb-3 mb-lg-5"
         value="{{ $data['name'] ?? '-' }}" readonly/>
</div>
@php
    $groupedPermissions = collect($data['permissions'] ?? [])
        ->groupBy(fn($perm) => $perm['category'] ?? 'Uncategorized');
@endphp

{{-- Permissions (mirip layout "Add Data") --}}
<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">List Permissions</label>

    <div id="permissions-wrapper" class="row g-9 mb-8">
        @if($groupedPermissions->isEmpty())
            <div class="text-muted px-3 py-2">Tidak ada permission.</div>
        @else
            @foreach($groupedPermissions as $category => $perms)
                <div class="col-md-4 fv-row">
                    <label class="fs-7 fw-semibold mb-2">{{ $category }}</label>
                    <div>
                        @foreach($perms as $perm)
                            <label class="form-check form-check-sm form-check-custom mb-2 me-5 me-lg-2 d-flex align-items-center">
                                <input class="form-check-input" type="checkbox"
                                       name="permission[]" 
                                       id="perm-{{ $perm['id'] }}"
                                       value="{{ $perm['id'] }}"
                                       checked disabled>
                                <span class="fw-semibold fs-7 ms-2">{{ $perm['name'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>



                      
                       
@php use Carbon\Carbon; @endphp

<div class="row">

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
