@props([
    'show' => false,
    'title' => '',
    'message' => '',
    'closeLabel' => '',
    'modalId' => 'front-alert-modal',
    'variant' => 'danger',
])

@php
    $shouldRender = $show && filled($message);
    $isSuccess = $variant === 'success';
@endphp

@if ($shouldRender)
    <dialog
        id="{{ $modalId }}"
        class="front-alert-modal w-[calc(100%-2rem)] max-w-md rounded-2xl border border-gray-200 bg-white p-0 text-left shadow-2xl [&::backdrop]:bg-gray-900/50"
        aria-labelledby="{{ $modalId }}-title"
        aria-describedby="{{ $modalId }}-desc"
    >
        <div class="p-6 sm:p-7">
            <div class="flex items-start gap-3">
                <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $isSuccess ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}"
                    aria-hidden="true"
                >
                    <i class="fas {{ $isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle' }} text-lg"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <h2 id="{{ $modalId }}-title" class="text-lg font-bold text-gray-900">{{ $title }}</h2>
                    <p id="{{ $modalId }}-desc" class="mt-2 text-sm leading-relaxed text-gray-600">{{ $message }}</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end border-t border-gray-100 pt-4">
                <button
                    type="button"
                    class="rounded-full bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-400 focus-visible:ring-offset-2"
                    data-front-alert-modal-close
                >
                    {{ $closeLabel }}
                </button>
            </div>
        </div>
    </dialog>

    @push('script')
        <script>
            (function () {
                var el = document.getElementById(@json($modalId));
                if (!el || typeof el.showModal !== 'function') {
                    return;
                }
                el.showModal();
                var closeBtn = el.querySelector('[data-front-alert-modal-close]');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function () {
                        el.close();
                    });
                }
                el.addEventListener('click', function (e) {
                    if (e.target === el) {
                        el.close();
                    }
                });
            })();
        </script>
    @endpush
@endif
