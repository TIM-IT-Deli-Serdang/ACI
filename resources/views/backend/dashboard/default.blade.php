@extends('backend.layout.app')
@section('title', 'Dashboard Overview')

@section('content')
    {{-- LOGIC PENGAMAN DATA (Agar tidak Error Array to String) --}}
    @php
        // 1. Ambil Nama (Cek apakah string atau bukan)
        $rawName = $user['name'] ?? $user['nama'] ?? 'Pengguna';
        $finalName = is_string($rawName) ? $rawName : 'Pengguna';

        // 2. Ambil Role (Cek apakah string, array, atau nested array)
        // Prioritas: key 'role' -> key 'roles' index 0
        $rawRole = $user['role'] ?? $user['roles'][0] ?? 'User';
        
        $finalRole = 'User';
        if (is_string($rawRole)) {
            // Jika langsung teks (misal: "admin")
            $finalRole = $rawRole;
        } elseif (is_array($rawRole)) {
            // Jika array (misal: ['id'=>1, 'name'=>'admin']), ambil key 'name' atau 'nama'
            $finalRole = $rawRole['name'] ?? $rawRole['nama'] ?? $rawRole[0] ?? 'User';
        }
    @endphp

    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Dashboard Overview
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a class="text-muted text-hover-primary">Home</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">Dashboard</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="row mb-6">
            <div class="col-12">
                <div class="card card-flush shadow-sm border border-gray-300 bg-light-primary">
                    <div class="card-body py-10 px-8">
                        <div class="row align-items-center">

                            <div class="col-lg-8">
                                <h1 class="fw-bold fs-2x text-gray-900 mb-3">
                                    👋 Selamat Datang,
                                    <span class="text-primary">
                                        {{-- GUNAKAN VARIABEL YANG SUDAH DIAMANKAN --}}
                                        {{ $finalName }}
                                    </span>
                                </h1>

                                <div class="fs-5 text-muted mb-4">
                                    Anda login sebagai
                                    <span class="fw-bold text-gray-800">
                                        {{-- GUNAKAN VARIABEL YANG SUDAH DIAMANKAN --}}
                                        {{ $finalRole }}
                                    </span>
                                </div>

                                <div class="fs-4 fw-semibold text-gray-700 mb-4">
                                    <span id="welcome-typing"></span>
                                </div>
                            </div>

                            <div class="col-lg-4 text-center d-none d-lg-block">
                                <img src="{{ asset('assets/media/illustrations/sigma-1/12.png') }}" alt="Welcome"
                                    class="img-fluid" style="max-height: 220px;" />
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('stylesheets')
        <meta name="csrf-token" content="{{ csrf_token() }}" />
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/plugins/custom/typedjs/typedjs.bundle.js') }}"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                new Typed("#welcome-typing", {
                    strings: [
                        "Aksi Cepat Infrastruktur.",
                        "Mewujudkan percepatan pembangunan.",
                        "Menghadirkan solusi infrastruktur cepat, tepat, dan berorientasi pada kemajuan nyata.",
                    ],
                    typeSpeed: 40,
                    backSpeed: 25,
                    backDelay: 2000,
                    loop: true,
                    showCursor: true,
                    cursorChar: "|"
                });
            });
        </script>
    @endpush
@endsection