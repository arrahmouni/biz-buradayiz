@extends('front::layouts.master')

@section('content')
    <x-front::page-hero
        :heading="$post->smartTrans('title')"
        :breadcrumb-label="__('front::home.blog_breadcrumb_label')"
    >
        <x-slot name="breadcrumb">
            <a href="{{ route('front.index') }}" class="hover:text-red-400 transition">{{ __('front::home.nav_home') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('front.blog.index') }}" class="hover:text-red-400 transition">{{ __('front::home.blog_heading') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white font-medium">{{ $post->smartTrans('title') }}</span>
        </x-slot>
        <x-slot name="belowDivider">
            @if ($post->published_at_iso_ll)
                <p class="text-gray-300 mt-4 text-sm">
                    <i class="far fa-calendar-alt mr-1"></i> {{ $post->published_at_iso_ll }}
                </p>
            @endif
        </x-slot>
    </x-front::page-hero>

    <article class="bg-gray-50 py-12 md:py-16">
        <div class="container mx-auto px-5 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-10">
                <div class="lg:w-2/3">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        @if ($post->front_cover_image_url !== '')
                            <div class="w-full max-h-96 overflow-hidden">
                                <img src="{{ $post->front_cover_image_url }}" alt="{{ $post->smartTrans('title') }}" class="w-full h-full object-cover max-h-96">
                            </div>
                        @endif
                        <div class="p-6 md:p-8 lg:p-10 text-gray-700 leading-relaxed
                                    [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:text-gray-800 [&_h2]:mt-8 [&_h2]:mb-4 [&_h2]:pb-2 [&_h2]:border-b [&_h2]:border-gray-200
                                    [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:text-gray-800 [&_h3]:mt-6 [&_h3]:mb-3
                                    [&_h4]:text-lg [&_h4]:font-semibold [&_h4]:mt-5 [&_h4]:mb-2
                                    [&_p]:mb-4 [&_p]:leading-relaxed
                                    [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:mb-4 [&_ul]:space-y-1
                                    [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:mb-4
                                    [&_li]:mb-1
                                    [&_a]:text-red-600 [&_a]:underline hover:[&_a]:text-red-800
                                    [&_strong]:font-semibold [&_strong]:text-gray-900
                                    [&_blockquote]:border-l-4 [&_blockquote]:border-red-300 [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:text-gray-600 [&_blockquote]:my-4
                                    [&_table]:w-full [&_table]:border-collapse [&_table]:my-4
                                    [&_th]:border [&_th]:border-gray-300 [&_th]:bg-gray-100 [&_th]:p-2 [&_th]:text-left
                                    [&_td]:border [&_td]:border-gray-300 [&_td]:p-2
                                    [&_img]:rounded-lg [&_img]:shadow-md [&_img]:my-4 [&_img]:max-w-full [&_img]:h-auto
                                    [&_figure]:my-6
                                    [&_figcaption]:text-sm [&_figcaption]:text-gray-500 [&_figcaption]:mt-2
                                    [&_.cms-embed-video]:relative [&_.cms-embed-video]:w-full [&_.cms-embed-video]:aspect-video [&_.cms-embed-video]:my-6 [&_.cms-embed-video]:overflow-hidden [&_.cms-embed-video]:rounded-lg [&_.cms-embed-video]:shadow-md
                                    [&_.cms-embed-video_iframe]:absolute [&_.cms-embed-video_iframe]:inset-0 [&_.cms-embed-video_iframe]:h-full [&_.cms-embed-video_iframe]:w-full [&_.cms-embed-video_iframe]:border-0
                                    [&_hr]:my-6 [&_hr]:border-gray-200">
                            {!! $post->smartTrans('long_description') !!}
                        </div>
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-wrap gap-4 justify-between items-center text-sm text-gray-500">
                            <a href="{{ route('front.blog.index') }}" class="inline-flex items-center text-red-600 font-medium hover:text-red-700 transition">
                                <i class="fas fa-arrow-left mr-2"></i> {{ __('front::home.blog_back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>

                <aside class="lg:w-1/3 space-y-8">
                    <div class="bg-white rounded-xl shadow-md p-5">
                        <h2 class="font-bold text-lg text-gray-800 border-b border-gray-200 pb-2 mb-4">{{ __('front::home.blog_search_title') }}</h2>
                        <form action="{{ route('front.blog.index') }}" method="GET" class="space-y-3">
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('front::home.blog_search_placeholder') }}" class="w-full border border-gray-300 rounded-full py-2 pl-10 pr-4 focus:ring-2 focus:ring-red-400 focus:outline-none">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none" aria-hidden="true"></i>
                            </div>
                        </form>
                    </div>

                    @if ($recentPosts->isNotEmpty())
                        <div class="bg-white rounded-xl shadow-md p-5">
                            <h2 class="font-bold text-lg text-gray-800 border-b border-gray-200 pb-2 mb-4">{{ __('front::home.blog_recent_title') }}</h2>
                            <ul class="space-y-3">
                                @foreach ($recentPosts as $recent)
                                    @if ($recent->front_blog_show_url)
                                        <li>
                                            <a href="{{ $recent->front_blog_show_url }}" class="flex gap-3 hover:text-red-600 transition">
                                                @if ($recent->front_cover_thumb_sidebar_url !== '')
                                                    <img src="{{ $recent->front_cover_thumb_sidebar_url }}" alt="" class="w-16 h-16 object-contain rounded-md shrink-0 bg-gray-100">
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
    </article>
@endsection
