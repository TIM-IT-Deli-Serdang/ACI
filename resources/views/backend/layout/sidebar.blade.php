<div id="kt_app_sidebar" class="app-sidebar align-self-start border border-gray-300 rounded" data-kt-drawer="true"
    data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    
    <div id="kt_app_sidebar_menu" class="app-sidebar-menu card w-100">
        <div class="hover-scroll-overlay-y mx-3 my-4" id="kt_app_sidebar_menu_wrapper" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
            data-kt-scroll-dependencies="#kt_app_header, #kt_app_toolbar"
            data-kt-scroll-wrappers="#kt_app_sidebar, #kt_app_sidebar_menu, #kt_app_content"
            data-kt-scroll-offset="5px">

            @php
                // Logika Cek Superadmin dari Session
                $sUser = session('user');
                $isSuperAdmin = false;
                $userName = 'User';
                $userRoleDisplay = 'Pengguna';

                if ($sUser) {
                    // Normalisasi ke array
                    $sUser = is_object($sUser) ? (array)$sUser : $sUser;
                    $userName = $sUser['name'] ?? 'User';
                    
                    $roles = $sUser['roles'] ?? [];
                    
                    foreach ($roles as $role) {
                        // Handle format role yang mungkin beda-beda (string/array/object)
                        $rName = is_array($role) ? ($role['name'] ?? '') : (is_object($role) ? $role->name : $role);
                        
                        if ($rName === 'Superadmin') {
                            $isSuperAdmin = true;
                        }
                        // Ambil role pertama untuk tampilan
                        if ($userRoleDisplay === 'Pengguna') $userRoleDisplay = $rName;
                    }
                }
            @endphp

            @if($isSuperAdmin)
                {{-- ================================================= --}}
                {{-- TAMPILAN KHUSUS SUPERADMIN: MENU MASTER DATA      --}}
                {{-- ================================================= --}}
                
                <div class="menu menu-column menu-rounded menu-sub-indention menu-active-bg menu-title-gray-800 menu-state-primary menu-arrow-gray-500 fw-semibold"
                    id="#kt_sidebar_menu" data-kt-menu="true">
                    
                    <div class="menu-item pt-1">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">Master Data</span>
                        </div>
                    </div>
                    @can('upt.list')
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('upt.index') ? 'active ' : '' }}"
                            href="{{ route('upt.index') }}">
                            <span class="menu-icon"><i class="ki-outline ki-rocket fs-2"></i></span>
                            <span class="menu-title">Data UPT</span>
                        </a>
                    </div>
                    @endcan

                    @can('kategori.list')
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('kategori.index') ? 'active ' : '' }}"
                            href="{{ route('kategori.index') }}">
                            <span class="menu-icon"><i class="ki-outline ki-category fs-2"></i></span>
                            <span class="menu-title">Kategori Laporan</span>
                        </a>
                    </div>
                    @endcan
                    
                </div>
                @else
                {{-- ================================================= --}}
                {{-- TAMPILAN NON-SUPERADMIN: WIDGET SELAMAT DATANG    --}}
                {{-- ================================================= --}}
                
                <div class="d-flex flex-column align-items-center text-center p-5">
                    <div class="symbol symbol-75px mb-5">
                        <div class="symbol-label bg-light-primary text-primary fs-1 fw-bold">
                            {{ substr($userName, 0, 1) }}
                        </div>
                    </div>

                    <div class="fs-4 fw-bold text-gray-900 mb-1">
                        Halo, {{ explode(' ', $userName)[0] }}!
                    </div>

                    <div class="fs-7 fw-semibold text-gray-500 mb-6">
                        {{ $userRoleDisplay }}
                    </div>

                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-7 text-gray-700">
                                    Selamat Datang! Silakan akses menu <b>Laporan</b> di bagian atas untuk memulai aktivitas Anda.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @endif

        </div>
        </div>
    </div>