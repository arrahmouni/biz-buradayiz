@extends('front::layouts.master')

@section('content')
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 text-white py-12 md:py-16">
        <div class="container mx-auto px-5 lg:px-8">
            <div class="max-w-3xl">
                <nav class="text-sm text-gray-300 mb-3">
                    <a href="{{ route('front.index') }}" class="hover:text-red-400 transition">{{ __('front::home.nav_home') }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-white font-medium">{{ __('front::home.faq_page_title') }}</span>
                </nav>
                <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight">
                    {{ __('front::home.faq_page_title') }}
                </h1>
                <p class="mt-4 text-gray-300 text-lg leading-relaxed">
                    {{ __('front::home.faq_page_intro') }}
                </p>
                <div class="w-16 h-1 bg-red-500 mt-4 rounded-full"></div>
            </div>
        </div>
    </div>

    <section class="bg-gray-50 py-12 md:py-16">
        <div class="container mx-auto px-5 lg:px-8">
            <div class="max-w-3xl mx-auto">
                @if ($faqs->isEmpty())
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center text-gray-600">
                        <i class="fas fa-circle-question text-red-500 text-3xl mb-3" aria-hidden="true"></i>
                        <p class="text-lg">{{ __('front::home.faq_empty') }}</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($faqs as $index => $faq)
                            <details class="group bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden open:ring-2 open:ring-red-100">
                                <summary class="cursor-pointer list-none flex items-center justify-between gap-4 px-6 py-5 text-left font-semibold text-gray-900 hover:bg-red-50/50 transition">
                                    <span class="flex items-start gap-3">
                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-700 text-sm font-bold" aria-hidden="true">
                                            {{ $index + 1 }}
                                        </span>
                                        <span>{{ $faq->smartTrans('title') }}</span>
                                    </span>
                                    <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform shrink-0" aria-hidden="true"></i>
                                </summary>
                                <div class="px-6 pb-6 pl-[4.25rem] text-gray-700 leading-relaxed border-t border-gray-100 pt-4
                                            [&_p]:mb-4 [&_p:last-child]:mb-0
                                            [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:mb-4 [&_ul]:space-y-1
                                            [&_a]:text-red-600 [&_a]:underline hover:[&_a]:text-red-800
                                            [&_strong]:font-semibold [&_strong]:text-gray-900">
                                    {!! $faq->smartTrans('long_description') !!}
                                </div>
                            </details>
                        @endforeach
                    </div>
                @endif

                <div class="mt-10 text-center text-sm text-gray-600">
                    <i class="fas fa-phone-alt text-red-500 mr-1" aria-hidden="true"></i>
                    {{ __('front::home.faq_still_questions') }}
                    <a href="{{ route('front.index') }}#contact" class="text-red-600 hover:underline font-medium">{{ __('front::home.contact_us') }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection
