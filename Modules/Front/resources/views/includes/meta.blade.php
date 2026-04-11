@php
    $pm = $pageMeta ?? [];
    $pageTitleInput = $title ?? null;
    $errorPageTitle = View::hasSection('error_page_title') ? trim(View::yieldContent('error_page_title')) : null;
    if (! empty($errorPageTitle)) {
        $title = config('app.name').' - '.$errorPageTitle;
    } elseif (! empty($pm['meta_title'])) {
        $title = $pm['meta_title'];
    } elseif ($pageTitleInput !== null && $pageTitleInput !== '') {
        $title = config('app.name').' - '.$pageTitleInput;
    } else {
        $title = trans('front::home.page_title');
    }
    $description = $pm['meta_description'] ?? View::getSection('meta_description') ?? trans('front::home.page_description');
    $keywords = $pm['meta_keywords'] ?? View::getSection('meta_keywords') ?? trans('front::home.page_keywords');
    $canonicalHref = ! empty($pm['canonical_url']) ? $pm['canonical_url'] : request()->url();
    $ogTitle = $pm['og_title'] ?? $title;
    $ogDescription = $pm['og_description'] ?? $description;
    $ogImageRaw = $pm['og_image'] ?? null;
    $ogImage = $ogImageRaw ? getFileUrl($ogImageRaw) : null;
    $robots = $pm['robots'] ?? null;
    if ($robots === null && View::hasSection('error_page_title')) {
        $robots = 'noindex, nofollow';
    }
@endphp

<title>{{ $title }}</title>
<meta charset="utf-8" />
<meta name="description" content="{{ $description }}"/>
<meta name="keywords" content="{{ $keywords }}" />
@if ($robots)
<meta name="robots" content="{{ $robots }}" />
@endif
<link rel="canonical" href="{{ $canonicalHref }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:url" content="{{ $canonicalHref }}">
@if ($ogImage)
<meta property="og:image" content="{{ $ogImage }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDescription }}">
@if ($ogImage)
<meta name="twitter:image" content="{{ $ogImage }}">
@endif
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!--begin::shortcut icon-->
<link rel="shortcut icon" href="{{ getSetting('app_favicon', asset('images/default/logos/favicon.png')) }}" />
<!--end::shortcut icon-->
