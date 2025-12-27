@extends('backend.layout.app')
@section('title', 'Validasi Laporan')
@section('content')

    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Validasi Laporan
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('laporan.index') }}" class="text-muted text-hover-primary">Laporan</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-gray-900">Validasi</li>
                </ul>
            </div>
            <div class="d-flex align-items-center pt-4 pb-7 pt-lg-1 pb-lg-2">
                <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-secondary">
                    <i class="ki-outline ki-arrow-left fs-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="row">
            <div class="col-xl-7">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Detail Laporan Warga</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Tinjau laporan sebelum memvalidasi</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        
                        <div class="mb-5 text-center">
                            @php
                                $storageBaseUrl = 'http://10.0.22.97/storage/laporan/masyarakat/';
                            @endphp
                            @if (!empty($data['file_masyarakat']))
                                <a href="{{ $storageBaseUrl . $data['file_masyarakat'] }}" target="_blank">
                                    <img src="{{ $storageBaseUrl . $data['file_masyarakat'] }}" class="rounded mw-100 shadow-sm"
                                        style="max-height: 300px; object-fit: cover;" alt="Bukti Laporan">
                                </a>
                            @else
                                <div class="alert alert-secondary d-flex align-items-center p-5">
                                    <i class="ki-outline ki-picture fs-2hx me-4"></i>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-dark">Tidak ada foto</h4>
                                        <span>Pelapor tidak menyertakan foto bukti.</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold">Deskripsi Masalah:</label>
                            <div class="p-3 bg-light rounded text-gray-800 fw-semibold">
                                {{ $data['deskripsi'] ?? '-' }}
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold">Alamat:</label>
                            <div class="text-gray-600">
                                {{ $data['alamat'] ?? '-' }}
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-6">
                                <label class="form-label fw-bold">Latitude:</label>
                                <input type="text" class="form-control form-control-solid form-control-sm" value="{{ $data['latitude'] ?? '-' }}" readonly>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Longitude:</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control form-control-solid" value="{{ $data['longitude'] ?? '-' }}" readonly>
                                    @if(!empty($data['latitude']) && !empty($data['longitude']))
                                        <a href="https://www.google.com/maps?q={{ $data['latitude'] }},{{ $data['longitude'] }}" target="_blank" class="btn btn-light-primary btn-icon">
                                            <i class="ki-outline ki-map"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Form Validasi</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Proses laporan ini</span>
                        </h3>
                        <div class="card-toolbar">
                            @switch($data['status_laporan'] ?? 0)
                                @case(1) <span class="badge badge-warning">Pending</span> @break
                                @case(2) <span class="badge badge-success">Disetujui</span> @break
                                @case(3) <span class="badge badge-danger">Ditolak</span> @break
                                @default <span class="badge badge-secondary">Draft</span>
                            @endswitch
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <form action="#" method="POST" id="formValidasi">
                            @csrf
                            
                            <div class="mb-10">
                                <label class="required form-label fw-bold">Keputusan Validasi</label>
                                <select name="status_validasi" class="form-select mb-2" data-control="select2" data-hide-search="true" data-placeholder="Pilih Status">
                                    <option></option>
                                    <option value="2">Setujui Laporan (Terima)</option>
                                    <option value="3">Tolak Laporan</option>
                                </select>
                                <div class="text-muted fs-7">Jika disetujui, laporan akan diteruskan ke dinas terkait.</div>
                            </div>

                            <div class="mb-10">
                                <label class="required form-label fw-bold">Catatan / Keterangan</label>
                                <textarea name="keterangan" class="form-control mb-2" rows="4" placeholder="Contoh: Laporan valid, akan segera ditindaklanjuti..."></textarea>
                            </div>

                            <div class="mb-10">
                                <label class="form-label fw-bold">Tanggal Validasi</label>
                                <input type="text" class="form-control form-control-solid" value="{{ date('d F Y') }}" readonly>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ki-outline ki-check-circle fs-2"></i> Simpan Validasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection