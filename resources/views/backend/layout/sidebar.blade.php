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
            <!--begin::Menu-->
            <div class="menu menu-column menu-rounded menu-sub-indention menu-active-bg menu-title-gray-800 menu-state-primary menu-arrow-gray-500 fw-semibold"
                id="#kt_sidebar_menu" data-kt-menu="true">
                <!--end:Menu item--><!--begin:Menu item-->
                <div class="menu-item pt-5">
                    <!--begin:Menu content-->
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Management Data</span>
                    </div>
                    <!--end:Menu content-->
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div class="menu-item">
                    <!--begin:Menu link--><a class="menu-link">
                        <span class="menu-icon"><i class="ki-outline ki-rocket fs-2"></i>
                        </span>
                        <span class="menu-title">Token List</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-abstract-13 fs-2"></i>
                        </span>
                        <span class="menu-title">Token Generate</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item--><!--begin:Menu item-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-abstract-26 fs-2"></i>
                        </span>
                        <span class="menu-title">Token Delete</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item--><!--begin:Menu item-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link"
                        href="https://preview.keenthemes.com/html/metronic/docs/getting-started/changelog"
                        target="_blank">
                        <span class="menu-icon">
                            <i class="ki-outline ki-code fs-2"></i>
                        </span>
                        <span class="menu-title">Token Update</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
            </div>
            <!--end::Menu-->
                @canany(['skpd.list', 'provinsi.list', 'kabupaten.list', 'kecamatan.list', 'desa.list'])

            <!--begin::Menu-->
            <div class="menu menu-column menu-rounded menu-sub-indention menu-active-bg menu-title-gray-800 menu-state-primary menu-arrow-gray-500 fw-semibold"
                id="#kt_sidebar_menu" data-kt-menu="true">
                <!--end:Menu item--><!--begin:Menu item-->
                <div class="menu-item pt-5">
                    <!--begin:Menu content-->
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Master Data</span>
                    </div>
                    <!--end:Menu content-->
                </div>
                <!--end:Menu item-->
                @can('skpd.list')
                <!--begin:Menu item-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('skpd.index') ? 'active ' : '' }}"
                        href="{{ route('skpd.index') }}">
                        <span class="menu-icon"><i class="ki-outline ki-rocket fs-2"></i>
                        </span>
                        <span class="menu-title">Data SKPD</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->    
                @endcan
                @canany(['provinsi.list', 'kabupaten.list', 'kecamatan.list', 'desa.list'])
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('provinsi.index','kabupaten.index','kecamatan.index','desa.index') ? 'show' : '' }}">
                    <!--begin:Menu link-->
                    <span class="menu-link  {{ request()->routeIs('provinsi.index','kabupaten.index','kecamatan.index','desa.index') ? 'active ' : '' }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-pointers fs-2">
                        </i>
                    </span>
                    <span class="menu-title">Wilayah</span><span class="menu-arrow">
                        </span>
                    </span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion" kt-hidden-height="121">

                    @can('provinsi.list')
                        <!--begin:Menu item-->
                        <div class="menu-item ">
                                <!--begin:Menu link-->
                                   <a class="menu-link {{ request()->routeIs('provinsi.index') ? 'active ' : '' }}"
                                        href="{{ route('provinsi.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot">
                                    </span>
                                </span>
                                <span class="menu-title">Provinsi</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            @endcan
                            @can('kabupaten.list')
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ request()->routeIs('kabupaten.index') ? 'active ' : '' }}"
                        href="{{ route('kabupaten.index') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot">
                                        </span>
                                    </span>
                                    <span class="menu-title">Kabupaten/Kota</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                @endcan
                                @can('kecamatan.list')
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{ request()->routeIs('kecamatan.index') ? 'active ' : '' }}"
                        href="{{ route('kecamatan.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot">
                                        </span>
                                    </span>
                                    <span class="menu-title">Kecamatan</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                @endcan
                                @can('desa.list')
                                 <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{ request()->routeIs('desa.index') ? 'active ' : '' }}"
                        href="{{ route('desa.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot">
                                        </span>
                                    </span>
                                    <span class="menu-title">Kelurahan/Desa</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                @endcan
                                </div>
                                <!--end:Menu sub-->
                            </div>
            
            <!--end::Menu-->
                @endcanany
                
            </div>
            <!--end::Menu-->
 @endcanany
           
        </div>
        <!--end::sidebar wrapper-->
    </div>
    <!--end::sidebar-->
</div>
