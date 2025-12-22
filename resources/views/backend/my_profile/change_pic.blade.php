<input type="hidden" name="hidden_id" id="hidden_id" value="{{ is_array(session('user') ?? $user ?? null) ? (session('user')['id'] ?? ($user['id'] ?? '')) : (optional(session('user') ?? $user)->id ?? '') }}" />

@php
    use Illuminate\Support\Str;

    $sessionUser = session('user');
    $user = $sessionUser ?? ($user ?? null);

    $baseUrl = rtrim(env('API_BASE_URL', ''), '/');

    // Ambil raw avatar dari berbagai kemungkinan struktur
    $rawAvatar = null;
    if ($user) {
        if (is_array($user)) {
            $rawAvatar = $user['avatar_url'] ?? $user['avatar'] ?? null;
        } else {
            $rawAvatar = $user->avatar_url ?? $user->avatar ?? null;
        }
    }

    // Tentukan avatarUrl final dengan pengecekan aman
    $avatarUrl = null;
    if (!empty($rawAvatar)) {
        if (Str::startsWith($rawAvatar, ['http://','https://'])) {
            // Jika ingin mengganti host jadi API_BASE_URL ketika diset:
            if (!empty($baseUrl)) {
                $parts = parse_url($rawAvatar);
                $path = $parts['path'] ?? '';
                $query = !empty($parts['query']) ? ('?' . $parts['query']) : '';
                $avatarUrl = $baseUrl . $path . $query;
            } else {
                $avatarUrl = $rawAvatar;
            }
        } else {
            // relatif -> gabungkan dengan baseUrl bila ada, atau anggap file di storage
            if (!empty($baseUrl)) {
                $avatarUrl = $baseUrl . '/' . ltrim($rawAvatar, '/');
            } else {
                $avatarUrl = asset('storage/user/avatar/' . ltrim($rawAvatar, '/'));
            }
        }
    } else {
        $avatarUrl = asset('assets/media/svg/files/blank-image.svg');
    }
@endphp

<!--begin::Input group-->
<div class="fv-row mb-7">
    <!--begin::Label-->
    <label class="d-block fw-semibold fs-6 mb-5">Avatar</label>
    <!--end::Label-->
    <!--begin::Image input-->
    <div class="image-input image-input-outline image-input-placeholder" data-kt-image-input="true">
        <!--begin::Preview existing avatar-->
        <div class="symbol symbol-125px symbol-125">
            <img id="preview-image-before-upload" src="{{ $avatarUrl }}" alt="avatar preview" class="img-fluid" />
        </div>
        <!--end::Preview existing avatar-->

        <!--begin::Label (change)-->
        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
               data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar" for="avatar">
            <i class="bi bi-pencil-fill fs-7"></i>
            <!--begin::Inputs (NO value attribute)-->
            <input type="file" name="avatar" id="avatar" accept=".png, .jpg, .jpeg" />
            <!--end::Inputs-->
        </label>
        <!--end::Label-->

        <!--begin::Cancel (hidden by default, shown via JS saat file dipilih)-->
        <span id="avatar-cancel" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
              data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar" style="display:none;">
            <i class="bi bi-x fs-2"></i>
        </span>
        <!--end::Cancel-->
    </div>
    <!--end::Image input-->

    <!--begin::Hint-->
    <div class="form-text">Allowed file types: png, jpg, jpeg. Max size: 2MB.</div>
    <!--end::Hint-->

    <span id="avatar-feedback" class="avatar-edit-invalid-feedback-changeavatar text-danger edit-show-validation-changeavatar d-none"></span>
</div>
<!--end::Input group-->

<script type="text/javascript">
    $(document).ready(function() {
        const MAX_SIZE = 2 * 1024 * 1024; // 2MB
        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];

        // simpan original untuk cancel
        $('#preview-image-before-upload').data('original-src', $('#preview-image-before-upload').attr('src'));

        function resetValidation() {
            $('#avatar').removeClass('is-invalid');
            $('#avatar-feedback').addClass('d-none').text('');
        }

        $('#avatar').on('change', function() {
            resetValidation();
            const fileInput = this;
            if (!fileInput.files || fileInput.files.length === 0) {
                $('#avatar-cancel').hide();
                return;
            }

            const file = fileInput.files[0];

            // validasi tipe
            if (!allowedTypes.includes(file.type)) {
                $('#avatar').addClass('is-invalid');
                $('#avatar-feedback').removeClass('d-none').text('Tipe file tidak didukung. Gunakan PNG atau JPG/JPEG.');
                fileInput.value = '';
                $('#avatar-cancel').hide();
                return;
            }

            // validasi ukuran
            if (file.size > MAX_SIZE) {
                $('#avatar').addClass('is-invalid');
                $('#avatar-feedback').removeClass('d-none').text('Ukuran file terlalu besar. Maksimum 2MB.');
                fileInput.value = '';
                $('#avatar-cancel').hide();
                return;
            }

            // tampilkan tombol cancel
            $('#avatar-cancel').show();

            // preview file
            if (window.FileReader) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview-image-before-upload').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            } else {
                $('#avatar-feedback').removeClass('d-none').text('Browser Anda tidak mendukung preview file.');
            }
        });

        // tombol cancel -> kembalikan preview awal dan kosongkan input
        $('#avatar-cancel').on('click', function(e) {
            e.preventDefault();
            $('#avatar').val('');
            const original = $('#preview-image-before-upload').data('original-src');
            $('#preview-image-before-upload').attr('src', original);
            $(this).hide();
            resetValidation();
        });
    });
</script>
