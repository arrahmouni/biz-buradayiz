@extends('front::layouts.master')

@section('content')
    <!-- Hero / Page Header (consistent with homepage) -->
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 text-white py-12 md:py-16">
        <div class="container mx-auto px-5 lg:px-8">
            <div class="max-w-3xl">
                <!-- Optional breadcrumb (dynamic) -->
                <nav class="text-sm text-gray-300 mb-3">
                    <a href="{{ route('front.index') }}" class="hover:text-red-400 transition">Home</a>
                    <span class="mx-2">/</span>
                    <span class="text-white font-medium">{{ $page->smartTrans('title') }}</span>
                </nav>
                <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight">
                    {{ $page->smartTrans('title') }}
                </h1>
                <div class="w-16 h-1 bg-red-500 mt-4 rounded-full"></div>
            </div>
        </div>
    </div>

    <!-- Main Content Area (card style) -->
    <article class="bg-gray-50 py-12 md:py-16">
        <div class="container mx-auto px-5 lg:px-8">
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">
                <!-- Optional decorative icon / illustration (matches Road Rescue brand) -->
                <div class="bg-red-50 px-6 py-3 border-b border-red-100 flex items-center gap-2">
                    <i class="fas fa-file-alt text-red-600 text-xl"></i>
                    <span class="text-gray-700 font-medium">{{ __('front::home.last_updated') }}: {{ now()->format('F d, Y') }}</span>
                </div>

                <!-- Dynamic CMS content -->
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
                    {!! $page->smartTrans('long_description') !!}
                </div>

                <!-- Footer note (optional call to action for contact) -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 text-center text-sm text-gray-500">
                    <i class="fas fa-question-circle text-red-400 mr-1"></i>
                    {{ __('front::home.questions_about_this_page') }}
                    <a href="{{ route('front.index') }}" class="text-red-600 hover:underline font-medium">{{ __('front::home.contact_us') }}</a>
                </div>
            </div>
        </div>
    </article>
@endsection
