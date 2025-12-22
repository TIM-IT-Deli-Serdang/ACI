@extends('backend.layout.app')
@section('title', 'Log Activity Detail')
@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8 mb-5">
    <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Log Activity Detail</h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted"><a class="text-muted text-hover-primary">Home</a></li>
                <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                <li class="breadcrumb-item text-muted">Help</li>
                <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                <li class="breadcrumb-item text-muted">Log Activity</li>
                <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                <li class="breadcrumb-item text-gray-900">{{ data_get($data, 'log_name', '-') }}</li>
            </ul>
        </div>
    </div>
</div>
<!--end::Toolbar-->

<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content">
        <div class="card border border-gray-300" id="kt_pricing">
            <div class="card-body p-lg-17">
                <div class="d-flex flex-column">
                    <div class="mb-13 text-center">
                        <h1 class="fs-2 fw-bold mb-5">
                            {{ data_get($data, 'causer.name', '-') }} {{ data_get($data, 'description', '-') }}
                        </h1>
                        <div class="text-gray-600 fw-semibold fs-5">
                            <label class="me-4">IP Address - {{ data_get($data, 'properties.ip', '-') }}</label>

                            @php
                                $agent = data_get($data, 'properties.agent', []);
                                $isDesktop = data_get($agent, 'is_desktop', false);
                                $isMobile = data_get($agent, 'is_mobile', false);
                                $deviceRaw = data_get($agent, 'device', 'Unknown');
                                $os = data_get($agent, 'os', '-');
                                $browser = data_get($agent, 'browser', '-');
                            @endphp

                            <label>
                                @if ($isDesktop)
                                    <i class="ki-outline ki-screen text-primary me-2"></i><label class="me-4">Desktop</label>
                                @elseif ($isMobile)
                                    <i class="ki-outline ki-tablet text-success me-2"></i><label class="me-4">Mobile</label>
                                @else
                                    <i class="ki-outline ki-question-2 text-danger me-2"></i>Unknown
                                @endif
                            </label>

                            <label class="me-4">{{ $os . ' - ' . $browser }}</label>
                        </div>
                    </div>

                    <div class="row g-10">
                        @if (data_get($data, 'properties.attributes') || data_get($data, 'properties.old'))
                            @if (data_get($data, 'properties.old'))
                                <div class="col-xl-6 ">
                            @else
                                <div class="col-xl-12">
                            @endif

                            <div class="card  h-100 align-items-center ">
                                <div class="w-100 d-flex flex-column flex-center rounded-3 bg-light bg-opacity-75 py-15 px-10 border border-gray-300">
                                    @if (data_get($data, 'properties.old'))
                                        <h1 class="fs-2 fw-bold mb-5">Data Baru</h1>
                                    @endif
                                    <div class="w-100 mb-10">
                                        <pre>{{ json_encode(data_get($data, 'properties.new', []), JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (data_get($data, 'properties.old'))
                            <div class="col-xl-6 ">
                                <div class="d-flex h-100 align-items-center">
                                    <div class="w-100 d-flex flex-column flex-center rounded-3 bg-light bg-opacity-75 py-15 px-10 border border-gray-300">
                                        <h1 class="fs-2 fw-bold mb-5">Data Sebelumnya</h1>
                                        <div class="w-100 mb-10 border-gray-300">
                                            @php
                                                $oldData = data_get($data, 'properties.old', []);
                                                if (isset($oldData['password'])) {
                                                    unset($oldData['password']);
                                                }
                                            @endphp
                                            <pre>{{ json_encode($oldData, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @else
                        <div class="col-xl-12 border-gray-300">
                            <div class="d-flex h-100 align-items-center">
                                <div class="w-100 d-flex flex-column flex-center rounded-3 bg-light bg-opacity-75 py-15 px-10 border border-gray-300">
                                    <div class="w-100 mb-10">
                                        <pre>{{ json_encode(data_get($data, 'properties', []), JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Plans-->
            </div>
        </div>
    </div>
</div>

@push('stylesheets')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@push('scripts')
@endpush
@endsection
