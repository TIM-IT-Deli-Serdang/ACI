<!DOCTYPE html>
<!--
Author: Keenthemes
Product Name: MetronicProduct Version: 8.3.2
Purchase: https://1.envato.market/EA4JP
Website: http://www.keenthemes.com
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.
-->
<html lang="en">
<!--begin::Head-->

<head>

    <title>@yield('title')</title>
    <meta charset="utf-8" />
    <meta name="description"
        content="
          Aplikasi ACI - Aksi Cepat Infrastruktur
        " />
    <meta name="keywords"
        content="
            aci, jalan, infrastruktur, laporan, deli serdang
        " />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title"
        content="Aplikasi ACI - Aksi Cepat Infrastruktur" />
    <meta property="og:url" content="#" />
    <meta property="og:site_name" content="Aplikasi ACI - Aksi Cepat Infrastruktur" />
    <link rel="canonical" href="#" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) 
        if (window.top != window.self) {
            window.top.location.replace(window.self.location.href);
        }
    </script>

    @stack('stylesheets')
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center">
    <!--begin::Theme mode setup on page load-->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <!--end::Theme mode setup on page load-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Page bg image-->
        <style>
            body {
                background-image: url('{{ asset('assets/media/patterns/chimox.svg') }}');
            }

            [data-bs-theme="dark"] body {
                background-image: url('{{ asset('assets/media/auth/bg10-dark.jpeg') }}');
            }
        </style>
        <!--end::Page bg image-->
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Aside-->
            <div class="d-flex flex-lg-row-fluid">
                <!--begin::Content-->
                <div class="d-flex flex-column flex-center pb-0 pb-lg-10 p-10 w-100">
                    <!--begin::Image-->
                    <img class="theme-light-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20 "
                        src="{{ asset('assets/media/logos/logo-aci.png') }}" alt="" />
                    <img class="theme-dark-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20"
                        src="{{ asset('assets/media/logos/logo-aci.png') }}" alt="" />
                    <!--end::Image-->
                    <h1 class="text-gray-800 fs-2qx fw-bold text-center mb-7">Aksi Cepat Infrastruktur</h1>
                    <div class="text-gray-600 fs-base text-center fw-semibold">
                        Mewujudkan percepatan pembangunan di
                        <a href="#" class="opacity-75-hover text-primary me-1">Kabupaten Deli Serdang</a>
                        melalui sistem kerja yang terintegrasi dan dinamis.
                        <br />
                        Kami berkomitmen menghadirkan solusi infrastruktur yang
                        <a href="#" class="opacity-75-hover text-primary me-1">cepat, tepat,</a>
                        dan berorientasi pada kemajuan nyata.
                    </div>
                </div>
                <!--end::Content-->
            </div>
            <!--begin::Aside-->
            <!--begin::Body-->
            @yield('content')
            <!--end::Body-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>
    <!--end::Root-->
    <!--begin::Javascript-->

    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <!--end::Global Javascript Bundle-->
    @stack('scripts')
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>
