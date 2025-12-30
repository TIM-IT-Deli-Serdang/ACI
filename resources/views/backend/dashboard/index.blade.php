@php
        // Normalisasi data user dari session (datang dari Controller sebagai array)
        // Ambil list nama role jika ada
        $userRoles = [];
        if (isset($user['roles']) && is_array($user['roles'])) {
            $userRoles = array_column($user['roles'], 'name');
        }
    @endphp

    @if(!empty(array_intersect(['Superadmin', 'Admin'], $userRoles)))
        @include('backend.dashboard.superadmin')
    @elseif(in_array('Developer', $userRoles))
        @include('backend.dashboard.developer')
    @else
        @include('backend.dashboard.default')
    @endif