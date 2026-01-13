@props([
    'id' => 'modal',
    'title' => '',
    'size' => 'lg',
    'footer' => true,
])

@php
    $sizeClasses = [
        'sm' => 'max-w-md',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        '2xl' => 'max-w-6xl',
    ];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['lg'];
@endphp

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity modal-overlay" data-modal-overlay></div>
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full {{ $sizeClass }}">
            <div class="bg-white px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-[#002740]" id="modal-title">
                        {{ $title }}
                    </h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none" data-modal-close>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="bg-white px-6 py-4">
                {{ $slot }}
            </div>
            @if($footer)
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const modalId = '{{ $id }}';
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const openButtons = document.querySelectorAll(`[data-modal-target="${modalId}"]`);
        const closeButtons = modal.querySelectorAll('[data-modal-close]');
        const overlay = modal.querySelector('[data-modal-overlay]');

        function openModal() {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        openButtons.forEach(btn => {
            btn.addEventListener('click', openModal);
        });

        closeButtons.forEach(btn => {
            btn.addEventListener('click', closeModal);
        });

        if (overlay) {
            overlay.addEventListener('click', closeModal);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    })();
</script>
@endpush

