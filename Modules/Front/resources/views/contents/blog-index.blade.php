@extends('front::layouts.master')

@section('content')
    <x-front::page-hero
        :heading="__('front::home.blog_heading')"
        :breadcrumb-label="__('front::home.blog_breadcrumb_label')"
    >
        <x-slot name="breadcrumb">
            <a href="{{ route('front.index') }}" class="hover:text-red-400 transition">{{ __('front::home.nav_home') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white font-medium">{{ __('front::home.blog_heading') }}</span>
        </x-slot>
        <x-slot name="belowDivider">
            <p class="text-gray-300 mt-4 text-lg max-w-2xl">
                {{ __('front::home.blog_intro') }}
            </p>
        </x-slot>
    </x-front::page-hero>

    <section class="bg-gray-50 py-12 md:py-16">
        <div class="container mx-auto px-5 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-10">
                <div class="lg:w-2/3">
                    @if ($blogs->isEmpty())
                        <div class="js-front-reveal front-reveal">
                            @include('front::includes.empty-state', [
                                'text' => __('front::home.blog_empty'),
                                'icon' => 'fas fa-newspaper',
                            ])
                        </div>
                    @else
                        <div class="space-y-8 js-front-reveal-group front-reveal-group">
                            @foreach ($blogs as $post)
                                <article class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-shadow front-reveal-child">
                                    @if ($post->front_blog_show_url && $post->front_cover_image_url !== '')
                                        <a href="{{ $post->front_blog_show_url }}">
                                            <x-front::placeholder-image
                                                :src="$post->front_cover_image_url"
                                                :alt="$post->smartTrans('title')"
                                                class="w-full h-56 md:h-64 object-cover"
                                            />
                                        </a>
                                    @endif
                                    <div class="p-6">
                                        @if ($post->published_at_iso_ll)
                                            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 mb-3">
                                                <span><i class="far fa-calendar-alt mr-1"></i> {{ $post->published_at_iso_ll }}</span>
                                            </div>
                                        @endif
                                        <h2 class="text-2xl font-bold text-gray-800 mb-2 hover:text-red-600 transition">
                                            @if ($post->front_blog_show_url)
                                                <a href="{{ $post->front_blog_show_url }}">{{ $post->smartTrans('title') }}</a>
                                            @else
                                                {{ $post->smartTrans('title') }}
                                            @endif
                                        </h2>
                                        @if ($post->front_blog_excerpt !== '')
                                            <p class="text-gray-600 mb-4">{{ $post->front_blog_excerpt }}</p>
                                        @endif
                                        @if ($post->front_blog_show_url)
                                            <a href="{{ $post->front_blog_show_url }}" class="inline-flex items-center text-red-600 font-semibold hover:text-red-700">
                                                {{ __('front::home.blog_read_more') }} <i class="fas fa-arrow-right ml-2"></i>
                                            </a>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        @if ($blogs->hasPages())
                            @php
                                $start = max($blogs->currentPage() - 2, 1);
                                $end = min($start + 4, $blogs->lastPage());
                                $start = max($end - 4, 1);
                            @endphp
                            <nav class="mt-10 flex justify-center js-front-reveal front-reveal" aria-label="{{ __('front::home.blog_pagination_label') }}">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if (! $blogs->onFirstPage())
                                        <a href="{{ $blogs->url($blogs->currentPage() - 1) }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                                            {{ __('front::home.blog_pagination_prev') }}
                                        </a>
                                    @endif
                                    @for ($page = $start; $page <= $end; $page++)
                                        @if ($page === $blogs->currentPage())
                                            <span class="px-3 py-2 bg-red-600 text-white rounded-md">{{ $page }}</span>
                                        @else
                                            <a href="{{ $blogs->url($page) }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">{{ $page }}</a>
                                        @endif
                                    @endfor
                                    @if ($blogs->hasMorePages())
                                        <a href="{{ $blogs->url($blogs->currentPage() + 1) }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                                            {{ __('front::home.blog_pagination_next') }}
                                        </a>
                                    @endif
                                </div>
                            </nav>
                        @endif
                    @endif
                </div>

                <aside class="lg:w-1/3 space-y-8 js-front-reveal-group front-reveal-group">
                    <div class="bg-white rounded-xl shadow-md p-5 front-reveal-child">
                        <h2 class="font-bold text-lg text-gray-800 border-b border-gray-200 pb-2 mb-4">{{ __('front::home.blog_search_title') }}</h2>
                        <form action="{{ route('front.blog.index') }}" method="GET" class="space-y-3">
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('front::home.blog_search_placeholder') }}" class="w-full border border-gray-300 rounded-full py-2 pl-10 pr-4 focus:ring-2 focus:ring-red-400 focus:outline-none">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none" aria-hidden="true"></i>
                            </div>
                        </form>
                    </div>

                    @if ($recentPosts->isNotEmpty())
                        <div class="bg-white rounded-xl shadow-md p-5 front-reveal-child">
                            <h2 class="font-bold text-lg text-gray-800 border-b border-gray-200 pb-2 mb-4">{{ __('front::home.blog_recent_title') }}</h2>
                            <ul class="space-y-3">
                                @foreach ($recentPosts as $recent)
                                    @if ($recent->front_blog_show_url)
                                        <li>
                                            <a href="{{ $recent->front_blog_show_url }}" class="flex gap-3 hover:text-red-600 transition">
                                                @if ($recent->front_cover_thumb_sidebar_url !== '')
                                                    <x-front::placeholder-image
                                                        :src="$recent->front_cover_thumb_sidebar_url"
                                                        alt=""
                                                        class="w-16 h-16 object-contain rounded-md shrink-0 bg-gray-100"
                                                    />
                                                @else
                                                    <span class="w-16 h-16 rounded-md bg-gray-100 flex items-center justify-center text-gray-400 shrink-0" aria-hidden="true">
                                                        <i class="fas fa-newspaper"></i>
                                                    </span>
                                                @endif
                                                <div>
                                                    <p class="text-sm font-semibold">{{ $recent->smartTrans('title') }}</p>
                                                    @if ($recent->published_at_relative)
                                                        <p class="text-xs text-gray-400">{{ $recent->published_at_relative }}</p>
                                                    @endif
                                                </div>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </section>
@endsection
