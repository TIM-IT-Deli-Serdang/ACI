@php
    // Cek Role Superadmin dari Session (Karena Auth Laravel tidak digunakan langsung)
    $sUser = session('user');
    $isSuperAdmin = false;

    if ($sUser) {
        // Handle jika session berupa Object atau Array
        $roles = is_object($sUser) ? ($sUser->roles ?? []) : ($sUser['roles'] ?? []);
        
        foreach ($roles as $role) {
            $roleName = is_object($role) ? $role->name : ($role['name'] ?? '');
            if ($roleName === 'Superadmin') {
                $isSuperAdmin = true;
                break;
            }
        }
    }
@endphp
<div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true"
    data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}"
    data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true"
    data-kt-swapper-mode="{default: 'append', lg: 'prepend'}"
    data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
    <!--begin::Menu-->
    <div class="menu menu-rounded menu-active-bg menu-state-primary menu-column menu-lg-row menu-title-gray-700 menu-icon-gray-500 menu-arrow-gray-500 menu-bullet-gray-500 my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0"
        id="kt_app_header_menu" data-kt-menu="true">

        <div
            class="menu-item menu-here-bg me-0 me-lg-2 menu-hover-bg menu-hover-bg-warning {{ request()->routeIs('dashboard.index') ? 'here show ' : '' }}">
            <a href="{{ route('dashboard.index') }}"
                class="menu-link px-4 {{ request()->routeIs('dashboard.index') ? 'active ' : '' }}">

                <span class="menu-title">Dashboards</span>
            </a>
        </div>

         <!--begin:Menu item-->
         <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start"
         class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
         <!--begin:Menu link-->
         <span class="menu-link py-3  {{ request()->routeIs('laporan.index','rekap.index') ? 'active ' : '' }}">
             <span class="menu-title">Laporan</span>
             <span class="menu-arrow d-lg-none">
             </span>
         </span>
         <!--end:Menu link-->
         <!--begin:Menu sub-->
         <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-200px">

         @can('laporan.list')
             <!--begin:Menu item-->
             <div class="menu-item {{ request()->routeIs('laporan.index') ? 'here show ' : '' }}">
                 <!--begin:Menu link-->
                 <a class="menu-link py-3 " href="{{ route('laporan.index') }}">
                     <span class="menu-icon">
                         <i class="ki-outline ki-rocket fs-2"></i>
                     </span>
                     <span class="menu-title">Laporan Masyarakat</span>
                 </a>
                 <!--end:Menu link-->
             </div>
             <!--end:Menu item-->
         @endcan
 @can('rekap.list')
             <!--begin:Menu item-->
             <div class="menu-item {{ request()->routeIs('rekap.index') ? 'here show ' : '' }}">
                 <!--begin:Menu link-->
                 <a class="menu-link py-3" href="{{ route('rekap.index') }}">
                     <span class="menu-icon">
                         <i class="ki-outline ki-code fs-2"></i>
                     </span>
                     <span class="menu-title">Rekap Laporan</span>
                 </a>
                 <!--end:Menu link-->
             </div>
             <!--end:Menu item-->
             @endcan                
         </div>
         <!--end:Menu sub-->
     </div>
     <!--end:Menu item-->
     @if($isSuperAdmin)
        <!--begin:Menu item-->
        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start"
            class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
            <!--begin:Menu link-->
            <span class="menu-link py-3  {{ request()->routeIs('user.index','role.index') ? 'active ' : '' }}">
                <span class="menu-title">Resources</span>
                <span class="menu-arrow d-lg-none">
                </span>
            </span>
            <!--end:Menu link-->
            <!--begin:Menu sub-->
            <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-200px">

            @can('user.list')
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('user.index') ? 'here show ' : '' }}">
                    <!--begin:Menu link-->
                    <a class="menu-link py-3 " href="{{ route('user.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-rocket fs-2"></i>
                        </span>
                        <span class="menu-title">User Management</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
            @endcan
    @can('role.list')
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('role.index') ? 'here show ' : '' }}">
                    <!--begin:Menu link-->
                    <a class="menu-link py-3" href="{{ route('role.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-code fs-2"></i>
                        </span>
                        <span class="menu-title">Role Management</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
                @endcan                
            </div>
            <!--end:Menu sub-->
        </div>
        <!--end:Menu item-->


        <!--begin:Menu item-->
        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start"
            class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
            <!--begin:Menu link-->
            <span class="menu-link py-3  {{ request()->routeIs('help.log-activity.index') ? 'active ' : '' }}">
                <span class="menu-title">Help</span>
                <span class="menu-arrow d-lg-none">
                </span>
            </span>
            <!--end:Menu link-->
            <!--begin:Menu sub-->
            <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-200px">
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('help.log-activity.index') ? 'here show ' : '' }}">
                    <!--begin:Menu link-->
                    <a class="menu-link py-3 " href="{{ route('help.log-activity.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-rocket fs-2"></i>
                        </span>
                        <span class="menu-title">Log Activity</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link py-3">
                        <span class="menu-icon">
                            <i class="ki-outline ki-code fs-2"></i>
                        </span>
                        <span class="menu-title">Changelog</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link py-3">
                        <span class="menu-icon">
                            <i class="ki-outline ki-abstract-26 fs-2"></i>
                        </span>
                        <span class="menu-title">Documentation</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
            </div>
            <!--end:Menu sub-->
        </div>
        <!--end:Menu item-->
        @endif 
        {{-- End if Superadmin --}}


    </div>
    <!--end::Menu-->
</div>
