@isset($options)
    @php
        $VALUE = array_merge([
            'id'                    => null,
            'name'                  => 'icon',
            'label'                 => null,
            'help'                  => null,
            'subText'               => null,
            'class'                 => null,
            'required'              => false,
            'placeholder'           => 'Select an icon',
            'value'                 => null,
            'view'                  => 'DEFAULT',
            'input_size'            => 'col-lg-8',
            'label_size'            => 'col-lg-12',
        ], $options);

        $VALUE['id'] = $VALUE['id'] ?? $VALUE['name'] . '_icon_picker';
        $VALUE['value'] = old($VALUE['name'], $VALUE['value']);
    @endphp

    @if($VALUE['view'] == 'DEFAULT')
        @include('admin::components.input_particles.label', ['VALUE' => $VALUE])
        @include('admin::components.input_particles.icon_picker', ['VALUE' => $VALUE])
    @elseif($VALUE['view'] == 'INLINE')
        <div class="row">
            @include('admin::components.input_particles.label', ['VALUE' => $VALUE])
            <div class="form-group {{ $VALUE['input_size'] }}">
                @include('admin::components.input_particles.icon_picker', ['VALUE' => $VALUE])
                <div class="form-text">
                    {{$VALUE['subText']}}
                </div>
            </div>
        </div>
    @endif

    @if ($VALUE['view'] == 'DEFAULT' && !empty($VALUE['subText']))
        <span class="form-text text-muted">{{$VALUE['subText']}}</span>
    @endif

    @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const iconPickerId = '{{$VALUE['id']}}';
                const iconInput = document.getElementById(iconPickerId);
                const iconPreview = document.getElementById(iconPickerId + '_preview');
                const iconModal = document.getElementById(iconPickerId + '_modal');

                if (!iconInput || !iconPreview || !iconModal) return;

                // Open modal when clicking preview
                iconPreview.addEventListener('click', function() {
                    const modal = new bootstrap.Modal(iconModal);
                    modal.show();
                });

                // Icon selection
                iconModal.addEventListener('click', function(e) {
                    const iconItem = e.target.closest('.icon-item');
                    if (iconItem) {
                        const iconClass = iconItem.getAttribute('data-icon');
                        const isEmoji = iconItem.getAttribute('data-is-emoji') === 'true';

                        iconInput.value = iconClass;

                        if (isEmoji) {
                            iconPreview.innerHTML = `<span style="font-size: 32px;">${iconClass}</span>`;
                        } else {
                            iconPreview.innerHTML = `<i class="${iconClass} fs-2x"></i>`;
                        }

                        iconPreview.setAttribute('data-icon', iconClass);

                        // Update the text input
                        const textInput = iconPreview.parentElement.querySelector('input[type="text"]');
                        if (textInput) {
                            textInput.value = iconClass;
                        }

                        const modalInstance = bootstrap.Modal.getInstance(iconModal);
                        modalInstance.hide();
                    }
                });

                // Search functionality
                const searchInput = iconModal.querySelector('.icon-search');
                if (searchInput) {
                    searchInput.addEventListener('input', function(e) {
                        const searchTerm = e.target.value.toLowerCase().trim();
                        const iconCols = iconModal.querySelectorAll('.icon-col');
                        const categories = iconModal.querySelectorAll('.icon-category');

                        if (searchTerm === '') {
                            // Show all icons and categories
                            iconCols.forEach(col => {
                                col.style.display = '';
                            });
                            categories.forEach(cat => {
                                cat.style.display = '';
                            });
                        } else {
                            // Hide/show icons based on search
                            let hasVisibleIcons = false;

                            iconCols.forEach(col => {
                                const iconName = (col.getAttribute('data-icon-name') || '').toLowerCase();
                                if (iconName.includes(searchTerm)) {
                                    col.style.display = '';
                                    hasVisibleIcons = true;
                                } else {
                                    col.style.display = 'none';
                                }
                            });

                            // Show/hide categories based on visible icons
                            categories.forEach(category => {
                                const categoryCols = category.querySelectorAll('.icon-col');
                                const visibleCols = Array.from(categoryCols).filter(col => col.style.display !== 'none');

                                if (visibleCols.length > 0) {
                                    category.style.display = '';
                                } else {
                                    category.style.display = 'none';
                                }
                            });
                        }
                    });
                }

                // Clear icon
                const clearBtn = iconModal.querySelector('.clear-icon');
                if (clearBtn) {
                    clearBtn.addEventListener('click', function() {
                        iconInput.value = '';
                        iconPreview.innerHTML = '<i class="fas fa-image fs-2x text-muted"></i>';
                        iconPreview.removeAttribute('data-icon');

                        // Update the text input
                        const textInput = iconPreview.parentElement.querySelector('input[type="text"]');
                        if (textInput) {
                            textInput.value = '';
                        }

                        const modalInstance = bootstrap.Modal.getInstance(iconModal);
                        modalInstance.hide();
                    });
                }
            });
        </script>
    @endpush
@endisset

