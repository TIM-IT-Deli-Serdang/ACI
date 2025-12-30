@php
    // 1. Ambil Raw Role dari berbagai kemungkinan key
    // Prioritas: role_name -> role -> roles (index 0)
    $rawRole = $user['role_name'] ?? $user['role'] ?? $user['roles'][0] ?? '';

    // 2. Normalisasi agar menjadi STRING (Teks)
    $userRole = '';
    
    if (is_string($rawRole)) {
        // Jika datanya langsung teks, misal "Superadmin"
        $userRole = $rawRole;
    } elseif (is_array($rawRole)) {
        // Jika datanya array, misal ['name' => 'Superadmin']
        $userRole = $rawRole['name'] ?? $rawRole['nama'] ?? '';
    }
@endphp

{{-- DEBUGGING (Hapus baris ini jika sudah tampil benar) --}}
{{-- 
<div class="alert alert-warning">
    Role Terdeteksi: <strong>{{ $userRole }}</strong> <br>
    Raw Data: @json($user)
</div> 
--}}

@if(in_array($userRole, ['Superadmin', 'upt', 'sda']))
    @include('backend.dashboard.superadmin')

@elseif($userRole == 'masyarakat')
    @include('backend.dashboard.masyarakat')

@else
    @include('backend.dashboard.default')
@endif