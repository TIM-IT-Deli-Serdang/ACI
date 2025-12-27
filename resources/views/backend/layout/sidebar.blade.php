<div id="kt_app_sidebar" class="app-sidebar align-self-start border border-gray-300 rounded" data-kt-drawer="true"
    data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::sidebar-->
    <div id="kt_app_sidebar_menu" class="app-sidebar-menu card w-100">
        <!--begin::sidebar wrapper-->
        <div class="hover-scroll-overlay-y mx-3 my-4" id="kt_app_sidebar_menu_wrapper" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
            data-kt-scroll-dependencies="#kt_app_header, #kt_app_toolbar"
            data-kt-scroll-wrappers="#kt_app_sidebar, #kt_app_sidebar_menu, #kt_app_content"
            data-kt-scroll-offset="5px">
           
                @canany(['upt.list'])

            <!--begin::Menu-->
            <div class="menu menu-column menu-rounded menu-sub-indention menu-active-bg menu-title-gray-800 menu-state-primary menu-arrow-gray-500 fw-semibold"
            id="#kt_sidebar_menu" data-kt-menu="true">
                <!--end:Menu item--><!--begin:Menu item-->
                <div class="menu-item pt-1">
                    <!--begin:Menu content-->
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Master Data</span>
                    </div>
                    <!--end:Menu content-->
                </div>
                <!--end:Menu item-->
                @can('upt.list')
                <!--begin:Menu item-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('upt.index') ? 'active ' : '' }}"
                        href="{{ route('upt.index') }}">
                        <span class="menu-icon"><i class="ki-outline ki-rocket fs-2"></i>
                        </span>
                        <span class="menu-title">Data UPT</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->    
                @endcan
                @can('kategori.list')
                <!--begin:Menu item-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('kategori.index') ? 'active ' : '' }}"
                        href="{{ route('kategori.index') }}">
                        <span class="menu-icon"><i class="ki-outline ki-rocket fs-2"></i>
                        </span>
                        <span class="menu-title">Kategori Laporan</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->    
                @endcan
               
                
            </div>
            <!--end::Menu-->
 @endcanany
           
        </div>
        <!--end::sidebar wrapper-->
    </div>
    <!--end::sidebar-->
</div>
