@php
    // Common FontAwesome icons organized by category
    $iconCategories = [
        'Technology' => [
            'fas fa-code', 'fas fa-laptop-code', 'fas fa-server', 'fas fa-database',
            'fas fa-cloud', 'fas fa-network-wired', 'fas fa-microchip', 'fas fa-mobile-alt',
            'fab fa-html5', 'fab fa-css3-alt', 'fab fa-js', 'fab fa-react', 'fab fa-vuejs',
            'fab fa-laravel', 'fab fa-php', 'fab fa-python', 'fab fa-node-js', 'fab fa-git-alt'
        ],
        'Design' => [
            'fas fa-palette', 'fas fa-paint-brush', 'fas fa-image', 'fas fa-images',
            'fas fa-eye', 'fas fa-magic', 'fas fa-brush', 'fas fa-crop', 'fas fa-adjust'
        ],
        'Business' => [
            'fas fa-briefcase', 'fas fa-chart-line', 'fas fa-handshake', 'fas fa-users',
            'fas fa-building', 'fas fa-dollar-sign', 'fas fa-chart-bar', 'fas fa-trophy',
            'fas fa-award', 'fas fa-star', 'fas fa-gem'
        ],
        'Communication' => [
            'fas fa-envelope', 'fas fa-phone', 'fas fa-comments', 'fas fa-comment-dots',
            'fas fa-paper-plane', 'fas fa-bullhorn', 'fas fa-share-alt', 'fas fa-link'
        ],
        'General' => [
            'fas fa-home', 'fas fa-user', 'fas fa-cog', 'fas fa-wrench', 'fas fa-tools',
            'fas fa-book', 'fas fa-graduation-cap', 'fas fa-lightbulb', 'fas fa-rocket',
            'fas fa-heart', 'fas fa-thumbs-up', 'fas fa-check-circle', 'fas fa-times-circle',
            'fas fa-info-circle', 'fas fa-exclamation-circle', 'fas fa-question-circle',
            'fas fa-arrow-right', 'fas fa-arrow-left', 'fas fa-arrow-up', 'fas fa-arrow-down',
            'fas fa-search', 'fas fa-filter', 'fas fa-sort', 'fas fa-calendar', 'fas fa-clock'
        ],
        'Emoji' => [
            '⚡', '⚛️', '🎨', '📘', '🚀', '🛠️', '🟢', '🐬', '🔗', '🐳', '📚', '💻', '🎯', '📬', '🐧',
            '🛒', '📋', '📊', '⛅', '💬', '🍳',
            '💾', '📝', '📢', '💳',
            '📧', '📱', '📍', '🐙', '💼', '🐦',
            '🔥', '⭐', '💡', '🏆', '💪', '🎉', '✨', '🌟', '🎭', '🎪', '🎬', '🌐', '🔧', '⚙️', '📈', '📉', '💎', '🔑'
        ]
    ];
@endphp

<div class="icon-picker-wrapper">
    <input type="hidden"
           name="{{$VALUE['name']}}"
           id="{{$VALUE['id']}}"
           value="{{$VALUE['value']}}"
           @if($VALUE['required']) required @endif>

    <div class="d-flex align-items-center gap-3">
        <div class="icon-preview"
             id="{{$VALUE['id']}}_preview"
             style="cursor: pointer; width: 60px; height: 60px; border: 2px dashed #ddd; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.3s;"
             data-bs-toggle="modal"
             data-bs-target="#{{$VALUE['id']}}_modal">
            @if($VALUE['value'])
                @php
                    $isEmojiValue = !str_starts_with($VALUE['value'], 'fa');
                @endphp
                @if($isEmojiValue)
                    <span style="font-size: 32px;">{{$VALUE['value']}}</span>
                @else
                    <i class="{{$VALUE['value']}} fs-2x"></i>
                @endif
            @else
                <i class="fas fa-image fs-2x text-muted"></i>
            @endif
        </div>

        <div class="flex-grow-1">
            <input type="text"
                   class="form-control"
                   placeholder="{{$VALUE['placeholder']}}"
                   value="{{$VALUE['value']}}"
                   readonly
                   style="cursor: pointer;"
                   data-bs-toggle="modal"
                   data-bs-target="#{{$VALUE['id']}}_modal">
        </div>
    </div>
</div>

<!-- Icon Picker Modal -->
<div class="modal fade" id="{{$VALUE['id']}}_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('admin::inputs.contents_crud.skills.icon_picker.title', ['default' => 'Select Icon'])</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-5">
                    <input type="text"
                           class="form-control icon-search"
                           placeholder="@lang('admin::inputs.contents_crud.skills.icon_picker.search', ['default' => 'Search icons...'])">
                </div>

                <div class="icon-categories">
                    @foreach($iconCategories as $category => $icons)
                        <div class="icon-category mb-5" data-category="{{$category}}">
                            <h6 class="mb-3 fw-bold">@lang('admin::inputs.contents_crud.skills.icon_picker.categories.' . $category, ['default' => $category])</h6>
                            <div class="row g-2">
                                @foreach($icons as $icon)
                                    @php
                                        $isEmoji = !str_starts_with($icon, 'fa');
                                        $iconClass = $isEmoji ? $icon : $icon;
                                        $iconName = $isEmoji ? $icon : str_replace(['fas fa-', 'far fa-', 'fab fa-'], '', $icon);
                                    @endphp
                                    <div class="col-auto icon-col" data-icon-name="{{$iconName}}">
                                        <div class="icon-item"
                                             data-icon="{{$iconClass}}"
                                             data-icon-name="{{$iconName}}"
                                             data-is-emoji="{{$isEmoji ? 'true' : 'false'}}"
                                             style="width: 50px; height: 50px; border: 1px solid #ddd; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;"
                                             onmouseover="this.style.borderColor='#0d6efd'; this.style.backgroundColor='#f0f8ff';"
                                             onmouseout="this.style.borderColor='#ddd'; this.style.backgroundColor='transparent';">
                                            @if($isEmoji)
                                                <span style="font-size: 24px;">{{$icon}}</span>
                                            @else
                                                <i class="{{$icon}} fs-4"></i>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light clear-icon">@lang('admin::base.reset', ['default' => 'Clear'])</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('admin::base.close')</button>
            </div>
        </div>
    </div>
</div>

