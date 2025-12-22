<input type="hidden" name="hidden_id" id="hidden_id" value="{{ $data['id'] ?? '' }}" />


 <!-- Input group: Nama Brand -->
                      

                        <!-- Input group: Nama Brand -->
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-7 mb-2">Nama User</label>
                            <input type="text" name="name" id="Editname" class="form-control mb-3 mb-lg-0"
                                placeholder="Contoh: Kominfo" value="{{ $data['name'] ?? '-' }}"/>
                            <span class="text-danger error-text name_error_edit"></span>
                        </div>

                        <div class="row">
                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">Password</label>
                                <input type="text" name="password" id="Editpassword" class="form-control mb-3 mb-lg-0"
                                    placeholder="Contoh: Jhon Hunter" />
                                <span class="text-danger error-text password_error_edit"></span>
                            </div>

                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">Role</label>
                            <select id="Editroles" name="roles" class="form-select mb-3 mb-lg-0"
                                    data-dropdown-parent="#Modal_Edit_Data"
                                    data-control="select2"
                                    data-placeholder="Pilih Role">
                                @php
                                    $selectedRole = null;
                                    if(!empty($data['roles']) && is_array($data['roles']) && count($data['roles']) > 0){
                                        $selectedRole = $data['roles'][0]; 
                                    }
                                @endphp

                                @if($selectedRole)
                                    <option value="{{ $selectedRole['id'] }}" selected>{{ $selectedRole['name'] }}</option>
                                @else
                                    <option value="">Pilih Role</option>
                                @endif
                            </select>

                            <span class="text-danger error-text role_error_edit"></span>

                            </div>
                        </div>

                        <div class="row">
                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">Email</label>
                                <input type="text" name="email" id="Editemail" class="form-control mb-3 mb-lg-0"
                                    placeholder="Contoh: Jhon Hunter" value="{{ $data['email'] ?? '-' }}"/>
                                <span class="text-danger error-text email_error_edit"></span>
                            </div>

                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">No. WhatsApp</label>
                                <input type="text" name="no_wa" id="Editno_wa" class="form-control mb-3 mb-lg-0"
                                    placeholder="Contoh: 1234 xxxx 5678" value="{{ $data['no_wa'] ?? '-' }}"/>
                                <span class="text-danger error-text no_wa_error_edit"></span>
                            </div>
                        </div>



 <script>
            $(document).ready(function() {

                //  select province:start
                $('#Editroles').select2({
                    dropdownParent: $("#Modal_Edit_Data"),

                    allowClear: true,
                    ajax: {
                        url: "{{ route('role.select') }}",
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.name,
                                        id: item.id
                                    }
                                })
                            };
                        }
                    }
                });
            });
        </script>
