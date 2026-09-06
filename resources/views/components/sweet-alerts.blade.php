<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Revisa la información',
                html: @json('<ul style="text-align:left;margin:0;padding-left:1.25rem;">'.collect($errors->all())->map(fn ($error) => '<li>'.e($error).'</li>')->implode('').'</ul>'),
                confirmButtonText: 'Corregir'
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Atención',
                text: @json(session('error')),
                confirmButtonText: 'Aceptar'
            });
        @elseif (session('status'))
            Swal.fire({
                icon: 'success',
                title: 'Listo',
                text: @json(session('status')),
                confirmButtonText: 'Aceptar'
            });
        @endif

        document.querySelectorAll('form[data-swal-confirm]').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                if (form.dataset.swalConfirmed === '1') {
                    return;
                }

                event.preventDefault();

                const result = await Swal.fire({
                    icon: form.dataset.swalIcon || 'warning',
                    title: form.dataset.swalTitle || '¿Confirmar acción?',
                    text: form.dataset.swalText || '',
                    showCancelButton: true,
                    confirmButtonText: form.dataset.swalConfirmText || 'Sí, continuar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                });

                if (result.isConfirmed) {
                    form.dataset.swalConfirmed = '1';
                    form.submit();
                }
            });
        });
    });
</script>
