@php
    // Common FontAwesome icons organized by category
    $iconCategories = [
        'services' => [
            'fas fa-car', 'fas fa-truck', 'fas fa-ambulance', 'fas fa-fire-extinguisher',
            'fas fa-wrench', 'fas fa-screwdriver', 'fas fa-hammer', 'fas fa-tools',
            'fas fa-gear', 'fas fa-battery-full', 'fas fa-gas-pump', 'fas fa-oil-can',
            'fas fa-car-side', 'fas fa-car-battery', 'fas fa-car-burst', 'fas fa-car-on', 'fas fa-car-rear',
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
            '📧', '📱', '📍', '🐙', '💼', '🐦','☕',
            '🔥', '⭐', '💡', '🏆', '💪', '🎉', '✨', '🌟', '🎭', '🎪', '🎬', '🌐', '🔧', '⚙️', '📈', '📉', '💎', '🔑','🔍'
        ],
    ];

    // SVG Images - scan for SVG files in project directories
    // $svgDirectories = [
    //     public_path('images'),
    //     // public_path('modules/admin/metronic/demo/media/svg'),
    // ];

    // $svgImages = [];
    // foreach ($svgDirectories as $directory) {
    //     if (is_dir($directory)) {
    //         $iterator = new \RecursiveIteratorIterator(
    //             new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
    //             \RecursiveIteratorIterator::SELF_FIRST
    //         );

    //         foreach ($iterator as $file) {
    //             if ($file->isFile() && strtolower($file->getExtension()) === 'svg') {
    //                 $relativePath = str_replace(public_path(), '', $file->getPathname());
    //                 $relativePath = str_replace('\\', '/', $relativePath);
    //                 $relativePath = ltrim($relativePath, '/');
    //                 $svgImages[] = $relativePath;
    //             }
    //         }
    //     }
    // }

    // // Add SVG images category if we found any
    // if (!empty($svgImages)) {
    //     $iconCategories['SVG Images'] = $svgImages;
    // }
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
             style="cursor: pointer; width: 60px; height: 60px; border: 2px dashed #ddd; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
            @if($VALUE['value'])
                @php
                    $iconValue = $VALUE['value'];
                    $isEmojiValue = !str_starts_with($iconValue, 'fa') && !str_ends_with(strtolower($iconValue), '.svg');
                    $isSvgValue = str_ends_with(strtolower($iconValue), '.svg') ||
                                  str_starts_with($iconValue, '/images/') ||
                                  str_starts_with($iconValue, '/modules/') ||
                                  str_starts_with($iconValue, 'images/') ||
                                  str_starts_with($iconValue, 'modules/');
                @endphp
                @if($isSvgValue)
                    @php
                        $svgPath = $iconValue;
                        if (!str_starts_with($svgPath, '/') && !str_starts_with($svgPath, 'http')) {
                            $svgPath = '/' . $svgPath;
                        }
                    @endphp
                    <img src="{{asset($svgPath)}}" alt="Icon" style="max-width: 40px; max-height: 40px; object-fit: contain;">
                @elseif($isEmojiValue)
                    <span style="font-size: 32px;">{{$iconValue}}</span>
                @else
                    <i class="{{$iconValue}} fs-2x"></i>
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
                <h5 class="modal-title">@lang('admin::strings.icon_picker.title', ['default' => 'Select Icon'])</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-5">
                    <input type="text"
                           class="form-control icon-search"
                           placeholder="@lang('admin::strings.icon_picker.search', ['default' => 'Search icons...'])">
                </div>

                <div class="icon-categories">
                    @foreach($iconCategories as $category => $icons)
                        <div class="icon-category mb-5" data-category="{{$category}}">
                            <h6 class="mb-3 fw-bold">@lang('admin::strings.icon_picker.categories.' . $category, ['default' => $category])</h6>
                            <div class="row g-2">
                                @foreach($icons as $icon)
                                    @php
                                        $isSvg = str_ends_with(strtolower($icon), '.svg') ||
                                                 str_starts_with($icon, '/images/') ||
                                                 str_starts_with($icon, '/modules/') ||
                                                 str_starts_with($icon, 'images/') ||
                                                 str_starts_with($icon, 'modules/');
                                        $isEmoji = !str_starts_with($icon, 'fa') && !$isSvg;
                                        $iconClass = $icon;
                                        $iconName = $isSvg ? basename($icon, '.svg') : ($isEmoji ? $icon : str_replace(['fas fa-', 'far fa-', 'fab fa-', 'fa-brands fa-'], '', $icon));
                                    @endphp
                                    <div class="col-auto icon-col" data-icon-name="{{$iconName}}">
                                        <div class="icon-item"
                                             data-icon="{{$iconClass}}"
                                             data-icon-name="{{$iconName}}"
                                             data-is-emoji="{{$isEmoji && !$isSvg ? 'true' : 'false'}}"
                                             data-is-svg="{{$isSvg ? 'true' : 'false'}}"
                                             style="width: 50px; height: 50px; border: 1px solid #ddd; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;"
                                             onmouseover="this.style.borderColor='#0d6efd'; this.style.backgroundColor='#f0f8ff';"
                                             onmouseout="this.style.borderColor='#ddd'; this.style.backgroundColor='transparent';">
                                            @if($isSvg)
                                                @php
                                                    $svgIconPath = $icon;
                                                    if (!str_starts_with($svgIconPath, '/') && !str_starts_with($svgIconPath, 'http')) {
                                                        $svgIconPath = '/' . $svgIconPath;
                                                    }
                                                @endphp
                                                <img src="{{asset($svgIconPath)}}" alt="{{$iconName}}" style="max-width: 32px; max-height: 32px; object-fit: contain;">
                                            @elseif($isEmoji)
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

