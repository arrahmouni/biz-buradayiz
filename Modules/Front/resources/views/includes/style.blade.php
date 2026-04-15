<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="{{ asset('css/page-loader.css') }}">
<link rel="stylesheet" href="{{ asset('modules/front/css/front.css') }}">
@php($frontHeroBackgroundImageUrl = getSetting('front_hero_background', config('front.default_hero_background_url')))
<style>
    :root {
        --front-hero-bg-image: url("{{ e($frontHeroBackgroundImageUrl) }}");
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
