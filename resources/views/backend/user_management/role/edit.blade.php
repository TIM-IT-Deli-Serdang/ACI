<input type="hidden" name="id" id="hidden_id" value="{{ $data['id'] ?? '' }}" />

<div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Role Name</label>
                <input type="text" name="name" id="edit_name"
                       class="form-control form-control-solid mb-3 mb-lg-0"
                       placeholder="Role Name"
                       value="{{ old('name', $role['name'] ?? ($role->name ?? '')) }}" />
                <span class="text-danger error-text name_error_edit"></span>
            </div>

            <div class="fv-row mb-7">
                <label class="required fw-semibold fs-6 mb-2">List Permissions</label>
                <span class="text-danger error-text permissions_error_edit"></span>

                <div id="permissions-wrapper-edit" class="row g-9 mb-8">
                    @foreach($permission as $category => $perms)
                        <div class="col-md-4 fv-row">
                            <label class="fs-6 fw-semibold mb-2">{{ $category }}</label>
                            <div>
                                @foreach($perms as $perm)
                                    @php
                                        $pid = $perm['id'] ?? $perm->id ?? null;
                                        $pname = $perm['name'] ?? $perm->name ?? '';
                                        $checked = in_array($pid, $rolePermissions) ? 'checked' : '';
                                    @endphp
                                    <label class="form-check form-check-sm form-check-custom mb-2 me-5 me-lg-2">
                                        <input class="form-check-input" type="checkbox"
                                               name="permissions[]"
                                               id="perm-edit-{{ $pid }}"
                                               value="{{ $pid }}"
                                               {{ $checked }}>
                                        <span class="form-check-label">{{ $pname }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>