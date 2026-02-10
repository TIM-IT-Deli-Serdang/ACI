@php
    // Normalisasi data user dari session (datang dari Controller sebagai array)
    // Ambil list nama role jika ada
    $userRoles = [];
    if (isset($user['roles']) && is_array($user['roles'])) {
        $userRoles = array_column($user['roles'], 'name');
    }
@endphp

@if (!empty(array_intersect(['Superadmin', 'admin', 'sda', 'upt'], $userRoles)))
    {{-- Tampilan Dashboard Internal --}}
    @include('backend.dashboard.superadmin')
@elseif(in_array('masyarakat', $userRoles))
    {{-- Tampilan Dashboard Masyarakat --}}
    @include('backend.dashboard.masyarakat')
@else
    {{-- Tampilan Default (Jika role tidak dikenali / belum login) --}}
    @include('backend.dashboard.default')
@endif
