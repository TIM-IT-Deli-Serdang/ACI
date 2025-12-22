<div class="app-navbar flex-shrink-0">
    <!--begin::Notifications-->
    <div class="app-navbar-item">
        <!--begin::Menu- wrapper-->
        <div class="btn btn-icon btn-icon-gray-600 btn-active-color-primary"
            data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
            data-kt-menu-placement="bottom">
            <i class="ki-outline ki-notification-on fs-1"></i>
        </div>

        <!--begin::Menu-->
        <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true"
            id="kt_menu_notifications" style="">
            <!--begin::Heading-->
            <div class="d-flex flex-column bgi-no-repeat rounded-top"
                style="background-image:url('{{ asset('assets/media/misc/menu-header-bg.jpg') }}')">
                <!--begin::Title-->
                <h3 class="text-white fw-semibold px-9 mt-10 mb-6">
                    Notifications <span class="fs-8 opacity-75 ps-3">24 reports</span>
                </h3>
                <!--end::Title-->

                <!--begin::Tabs-->
                <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-semibold px-9" role="tablist">



                    <li class="nav-item" role="presentation">
                        <a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active" data-bs-toggle="tab"
                            href="#kt_topbar_notifications_3" aria-selected="true" tabindex="-1"
                            role="tab">Logs</a>
                    </li>
                </ul>
                <!--end::Tabs-->
            </div>
            <!--end::Heading-->

            <!--begin::Tab content-->
            <div class="tab-content">


                <!--begin::Tab panel-->
                <div class="tab-pane fade show active" id="kt_topbar_notifications_3" role="tabpanel">
                    <!--begin::Items-->
                    <div class="scroll-y mh-325px my-5 px-8">
                        <!--begin::Item-->
                        <div class="d-flex flex-stack py-4">
                            <!--begin::Section-->
                            <div class="d-flex align-items-center me-2">
                                <!--begin::Code-->
                                <span class="w-70px badge badge-light-success me-4">200 OK</span>
                                <!--end::Code-->

                                <!--begin::Title-->
                                <a href="#" class="text-gray-800 text-hover-primary fw-semibold">New order</a>
                                <!--end::Title-->
                            </div>
                            <!--end::Section-->

                            <!--begin::Label-->
                            <span class="badge badge-light fs-8">Just now</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="d-flex flex-stack py-4">
                            <!--begin::Section-->
                            <div class="d-flex align-items-center me-2">
                                <!--begin::Code-->
                                <span class="w-70px badge badge-light-danger me-4">500 ERR</span>
                                <!--end::Code-->

                                <!--begin::Title-->
                                <a href="#" class="text-gray-800 text-hover-primary fw-semibold">New
                                    customer</a>
                                <!--end::Title-->
                            </div>
                            <!--end::Section-->

                            <!--begin::Label-->
                            <span class="badge badge-light fs-8">2 hrs</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="d-flex flex-stack py-4">
                            <!--begin::Section-->
                            <div class="d-flex align-items-center me-2">
                                <!--begin::Code-->
                                <span class="w-70px badge badge-light-success me-4">200 OK</span>
                                <!--end::Code-->

                                <!--begin::Title-->
                                <a href="#" class="text-gray-800 text-hover-primary fw-semibold">Payment
                                    process</a>
                                <!--end::Title-->
                            </div>
                            <!--end::Section-->

                            <!--begin::Label-->
                            <span class="badge badge-light fs-8">5 hrs</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="d-flex flex-stack py-4">
                            <!--begin::Section-->
                            <div class="d-flex align-items-center me-2">
                                <!--begin::Code-->
                                <span class="w-70px badge badge-light-warning me-4">300 WRN</span>
                                <!--end::Code-->

                                <!--begin::Title-->
                                <a href="#" class="text-gray-800 text-hover-primary fw-semibold">Search
                                    query</a>
                                <!--end::Title-->
                            </div>
                            <!--end::Section-->

                            <!--begin::Label-->
                            <span class="badge badge-light fs-8">2 days</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="d-flex flex-stack py-4">
                            <!--begin::Section-->
                            <div class="d-flex align-items-center me-2">
                                <!--begin::Code-->
                                <span class="w-70px badge badge-light-success me-4">200 OK</span>
                                <!--end::Code-->

                                <!--begin::Title-->
                                <a href="#" class="text-gray-800 text-hover-primary fw-semibold">API
                                    connection</a>
                                <!--end::Title-->
                            </div>
                            <!--end::Section-->

                            <!--begin::Label-->
                            <span class="badge badge-light fs-8">1 week</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="d-flex flex-stack py-4">
                            <!--begin::Section-->
                            <div class="d-flex align-items-center me-2">
                                <!--begin::Code-->
                                <span class="w-70px badge badge-light-success me-4">200 OK</span>
                                <!--end::Code-->

                                <!--begin::Title-->
                                <a href="#" class="text-gray-800 text-hover-primary fw-semibold">Database
                                    restore</a>
                                <!--end::Title-->
                            </div>
                            <!--end::Section-->

                            <!--begin::Label-->
                            <span class="badge badge-light fs-8">Mar 5</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="d-flex flex-stack py-4">
                            <!--begin::Section-->
                            <div class="d-flex align-items-center me-2">
                                <!--begin::Code-->
                                <span class="w-70px badge badge-light-warning me-4">300 WRN</span>
                                <!--end::Code-->

                                <!--begin::Title-->
                                <a href="#" class="text-gray-800 text-hover-primary fw-semibold">System
                                    update</a>
                                <!--end::Title-->
                            </div>
                            <!--end::Section-->

                            <!--begin::Label-->
                            <span class="badge badge-light fs-8">May 15</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="d-flex flex-stack py-4">
                            <!--begin::Section-->
                            <div class="d-flex align-items-center me-2">
                                <!--begin::Code-->
                                <span class="w-70px badge badge-light-warning me-4">300 WRN</span>
                                <!--end::Code-->

                                <!--begin::Title-->
                                <a href="#" class="text-gray-800 text-hover-primary fw-semibold">Server OS
                                    update</a>
                                <!--end::Title-->
                            </div>
                            <!--end::Section-->

                            <!--begin::Label-->
                            <span class="badge badge-light fs-8">Apr 3</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="d-flex flex-stack py-4">
                            <!--begin::Section-->
                            <div class="d-flex align-items-center me-2">
                                <!--begin::Code-->
                                <span class="w-70px badge badge-light-warning me-4">300 WRN</span>
                                <!--end::Code-->

                                <!--begin::Title-->
                                <a href="#" class="text-gray-800 text-hover-primary fw-semibold">API
                                    rollback</a>
                                <!--end::Title-->
                            </div>
                            <!--end::Section-->

                            <!--begin::Label-->
                            <span class="badge badge-light fs-8">Jun 30</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="d-flex flex-stack py-4">
                            <!--begin::Section-->
                            <div class="d-flex align-items-center me-2">
                                <!--begin::Code-->
                                <span class="w-70px badge badge-light-danger me-4">500 ERR</span>
                                <!--end::Code-->

                                <!--begin::Title-->
                                <a href="#" class="text-gray-800 text-hover-primary fw-semibold">Refund
                                    process</a>
                                <!--end::Title-->
                            </div>
                            <!--end::Section-->

                            <!--begin::Label-->
                            <span class="badge badge-light fs-8">Jul 10</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="d-flex flex-stack py-4">
                            <!--begin::Section-->
                            <div class="d-flex align-items-center me-2">
                                <!--begin::Code-->
                                <span class="w-70px badge badge-light-danger me-4">500 ERR</span>
                                <!--end::Code-->

                                <!--begin::Title-->
                                <a href="#" class="text-gray-800 text-hover-primary fw-semibold">Withdrawal
                                    process</a>
                                <!--end::Title-->
                            </div>
                            <!--end::Section-->

                            <!--begin::Label-->
                            <span class="badge badge-light fs-8">Sep 10</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="d-flex flex-stack py-4">
                            <!--begin::Section-->
                            <div class="d-flex align-items-center me-2">
                                <!--begin::Code-->
                                <span class="w-70px badge badge-light-danger me-4">500 ERR</span>
                                <!--end::Code-->

                                <!--begin::Title-->
                                <a href="#" class="text-gray-800 text-hover-primary fw-semibold">Mail tasks</a>
                                <!--end::Title-->
                            </div>
                            <!--end::Section-->

                            <!--begin::Label-->
                            <span class="badge badge-light fs-8">Dec 10</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Item-->

                    </div>
                    <!--end::Items-->

                    <!--begin::View more-->
                    <div class="py-3 text-center border-top">
                        <a href="/metronic8/demo35/pages/user-profile/activity.html"
                            class="btn btn-color-gray-600 btn-active-color-primary">
                            View All
                            <i class="ki-outline ki-arrow-right fs-5"></i> </a>
                    </div>
                    <!--end::View more-->
                </div>
                <!--end::Tab panel-->
            </div>
            <!--end::Tab content-->
        </div>
        <!--end::Menu--> <!--end::Menu wrapper-->
    </div>
    <!--end::Notifications-->
@php
    $user = session('user') ?? $user ?? null;
    $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
    $rawAvatar = $user['avatar_url'] ?? null;
    $avatarUrl = null;
    if (!empty($rawAvatar)) {
        $avatarUrl = preg_replace('#^https?://[^/]+#i', $baseUrl, $rawAvatar);
    } elseif (!empty($user['avatar'])) {
        $avatarUrl = $baseUrl . '/storage/user/avatar/' . $user['avatar'];
    }
@endphp
    <!--begin::User menu-->
    <div class="app-navbar-item ms-3 ms-lg-9" id="kt_header_user_menu_toggle">
        <!--begin::Menu wrapper-->
        <div class="d-flex align-items-center" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
            data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
            <!--begin:Info-->
            <div class="text-end d-none d-sm-flex flex-column justify-content-center me-3">
                <span class="text-gray-500 fs-8 fw-bold">Hello</span>
                <a
                    class="text-gray-800 text-hover-primary fs-7 fw-bold d-block navbar-name">{{ ucwords(strtolower($user['name'])) }}</a>
            </div>
            <!--end:Info-->
            <!--begin::User-->
            <div class="symbol symbol-40px me-5">

              @if (!empty($avatarUrl))
    <img class="navbar-avatar-img" 
         src="{{ $avatarUrl }}" 
         alt="{{ $user['name'] ?? 'User' }}" />
@else
    <div class="symbol-label fs-3 bg-light-primary text-primary navbar-avatar-wrapper navbar-avatar-name">
        {{ strtoupper(substr($user['name'] ?? 'U', 0, 1)) }}
    </div>
@endif
                <div
                    class="position-absolute translate-middle bottom-0 mb-1 start-100 ms-n1 bg-success rounded-circle h-8px w-8px">
                </div>
            </div>

            <!--end::User-->
        </div>
        <!--begin::User account menu-->
        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px border border-gray-300 shadow-sm"
            data-kt-menu="true">
            <!--begin::Menu item-->
            <div class="menu-item px-3">
                <div class="menu-content d-flex align-items-center px-3">
                    <!--begin::Avatar-->
                    <div class="symbol symbol-50px me-5">

                        @if (!empty($avatarUrl)) 
                            <img class="sidebar-avatar-img"
                                src="{{ $avatarUrl }}"
                                alt="{{ $user['name'] }}" />
                        @else
                            <div
                                class="symbol-label fs-3 bg-light-primary text-primary sidebar-avatar-wrapper navbar-avatar-sidebar-name">
                                {{ strtoupper(substr($user['name'], 0, 1)) }}
                            </div>
                        @endif

                    </div>

                    <!--end::Avatar-->
                    <!--begin::Username-->
                    <div class="d-flex flex-column">
                        <div class="fw-bold d-flex align-items-center fs-5 navbar-sidebar-name">
                            {{ ucwords(strtolower($user['name'])) }}

                        </div>
                        <a class="fw-semibold text-muted text-hover-primary fs-7 navbar-sidebar-no_wa">
                            {{ ucwords(strtolower($user['no_wa'])) }}
                        </a>
                    </div>
                    <!--end::Username-->
                </div>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu separator-->
            <div class="separator my-2 border-gray-300"></div>
            <!--end::Menu separator-->
            <!--begin::Menu item-->
            <div class="menu-item px-5">
                <a href="{{ route('my-profile.index') }}" class="menu-link px-5"> My Profile </a>
            </div>
            <!--end::Menu item-->

            <!--begin::Menu item-->
            <div class="menu-item px-5 my-1">
                <a href="#" class="menu-link px-5">
                    Change Password
                </a>
            </div>
            <!--end::Menu item-->

            <!--begin::Menu separator-->
            <div class="separator my-2 border-gray-300"></div>
            <!--end::Menu separator-->
            <!--begin::Menu item-->
            <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                <a href="#" class="menu-link px-5">
                    <span class="menu-title position-relative">
                        Mode
                        <span class="ms-5 position-absolute translate-middle-y top-50 end-0">
                            <i class="ki-outline ki-night-day theme-light-show fs-2"></i>
                            <i class="ki-outline ki-moon theme-dark-show fs-2"></i>
                        </span>
                    </span>
                </a>
                <!--begin::Menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                    data-kt-menu="true" data-kt-element="theme-mode-menu">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3 my-0">
                        <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                            <span class="menu-icon" data-kt-element="icon">
                                <i class="ki-outline ki-night-day fs-2"></i>
                            </span>
                            <span class="menu-title"> Light </span>
                        </a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3 my-0">
                        <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                            <span class="menu-icon" data-kt-element="icon">
                                <i class="ki-outline ki-moon fs-2"></i>
                            </span>
                            <span class="menu-title"> Dark </span>
                        </a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3 my-0">
                        <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                            <span class="menu-icon" data-kt-element="icon">
                                <i class="ki-outline ki-screen fs-2"></i>
                            </span>
                            <span class="menu-title"> System </span>
                        </a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::Menu-->
            </div>
            <!--end::Menu item-->


            <!--begin::Menu item-->

            <div class="menu-item px-5">
                <a href="#" class="menu-link px-5" id="btn-logout">
                    Sign Out
                </a>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>

            <!--end::Menu item-->
        </div>
        <!--end::User account menu-->
        <!--end::Menu wrapper-->
    </div>
    <!--end::User menu-->
    <!--begin::Header menu toggle-->
    <div class="app-navbar-item d-lg-none ms-2 me-n3" title="Show header menu">
        <div class="btn btn-icon btn-color-gray-500 btn-active-color-primary w-35px h-35px"
            id="kt_app_sidebar_mobile_toggle">
            <i class="ki-outline ki-text-align-left fs-1"></i>
        </div>
    </div>
    <!--end::Header menu toggle-->
</div>
<script>
    document.getElementById('btn-logout').addEventListener('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: "Keluar dari Aplikasi?",
            html: `
            <div class="text-muted">Anda yakin ingin logout?</div>
        `,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Logout",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
        }).then((result) => {
            if (result.isConfirmed) {

                // === SweetAlert Loader Premium ===
                Swal.fire({
                    title: "<b>Memproses Logout...</b>",
                    html: `
                    <div style="display:flex; justify-content:center; gap:10px; margin-top:20px;">
                        <div class="dot-loader"></div>
                        <div class="dot-loader dot-loader--2"></div>
                        <div class="dot-loader dot-loader--3"></div>
                    </div>
                `,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    timer: 1500,

                }).then(() => {
                    document.getElementById('logout-form').submit();
                });

                // Tambahkan style loader
                const styleDots = document.createElement('style');
                styleDots.textContent = `
                .dot-loader {
                    width: 12px;
                    height: 12px;
                    background-color: #d33;
                    border-radius: 50%;
                    animation: bounceDot 0.6s infinite alternate;
                }
                .dot-loader--2 { animation-delay: 0.15s; }
                .dot-loader--3 { animation-delay: 0.3s; }

                @keyframes bounceDot {
                    0% { transform: translateY(0); opacity: 1; }
                    100% { transform: translateY(-10px); opacity: 0.4; }
                }
            `;
                document.head.appendChild(styleDots);
            }
        });
    });
</script>
