<input type="hidden" name="hidden_id" id="hidden_id" value="{{ $data['id'] ?? '' }}" />


 <!-- Input group: Nama Brand -->
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-7 mb-2">Kode SKPD</label>
                            <input type="text" name="kode_skpd" id="kode_skpd" class="form-control mb-3 mb-lg-0"
                                placeholder="Contoh: 1.3302.03" value="{{ $data['kode_skpd'] ?? '-' }}"/>
                            <span class="text-danger error-text kode_skpd_error_edit"></span>
                        </div>

                        <!-- Input group: Nama Brand -->
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-7 mb-2">Nama SKPD</label>
                            <input type="text" name="nama_skpd" id="nama_skpd" class="form-control mb-3 mb-lg-0"
                                placeholder="Contoh: Kominfo" value="{{ $data['nama_skpd'] ?? '-' }}"/>
                            <span class="text-danger error-text nama_skpd_error_edit"></span>
                        </div>

                        <div class="row">
                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">Kepala SKPD</label>
                                <input type="text" name="kepala_skpd" id="kepala_skpd" class="form-control mb-3 mb-lg-0"
                                    placeholder="Contoh: Jhon Hunter" value="{{ $data['kepala_skpd'] ?? '-' }}"/>
                                <span class="text-danger error-text kepala_skpd_error_edit"></span>
                            </div>

                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">NIP Kepala SKPD</label>
                                <input type="text" name="nip_kepala" id="nip_kepala" class="form-control mb-3 mb-lg-0"
                                    placeholder="Contoh: 1234 xxxx 5678" value="{{ $data['nip_kepala'] ?? '-' }}"/>
                                <span class="text-danger error-text nip_kepala_error_edit"></span>
                            </div>
                        </div>


                        <div class="row">
                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">latitude</label>
                                <input type="text" name="latitude" id="latitude" class="form-control mb-3 mb-lg-0"
                                    placeholder="Contoh: -6.208806" value="{{ $data['latitude'] ?? '-' }}"/>
                                <span class="text-danger error-text latitude_error_edit"></span>
                            </div>

                            <!-- Input group: Nama Brand -->
                            <div class="fv-row mb-7 col-6">
                                <label class="required fw-semibold fs-7 mb-2">longitude</label>
                                <input type="text" name="longitude" id="longitude" class="form-control mb-3 mb-lg-0"
                                    placeholder="Contoh: 106.845175" value="{{ $data['longitude'] ?? '-' }}"/>
                                <span class="text-danger error-text longitude_error_edit"></span>
                            </div>
                        </div>

                      