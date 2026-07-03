<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const successMessage = @json(session('success') ?? session('status'));
        const errorMessage = @json(session('error'));
        const validationMessage = @json($errors->any() ? $errors->first() : null);

        if (successMessage) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: successMessage,
                confirmButtonText: 'OK',
                confirmButtonColor: '#2563eb'
            });
        } else if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: errorMessage,
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626'
            });
        } else if (validationMessage) {
            Swal.fire({
                icon: 'warning',
                title: 'Form Belum Lengkap',
                text: validationMessage,
                confirmButtonText: 'Perbaiki',
                confirmButtonColor: '#d97706'
            });
        }

        document.querySelectorAll('form').forEach(function (form) {
            const methodInput = form.querySelector('input[name="_method"]');
            const isDeleteForm = methodInput && methodInput.value.toUpperCase() === 'DELETE';

            if (!isDeleteForm || form.dataset.swalDeleteBound === '1') return;
            form.dataset.swalDeleteBound = '1';
            form.onsubmit = null;

            form.addEventListener('submit', function (event) {
                if (form.dataset.swalConfirmed === '1') return;

                event.preventDefault();
                event.stopImmediatePropagation();

                Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data ini?',
                    text: 'Data yang dihapus mungkin tidak bisa dikembalikan.',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.dataset.swalConfirmed = '1';
                        form.submit();
                    }
                });
            }, true);
        });
    });
</script>
