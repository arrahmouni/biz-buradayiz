@extends('front::layouts.master')

@section('content')
    <x-front::page-hero :heading="__('front::home.faq_page_title')">
        <x-slot name="breadcrumb">
            <a href="{{ route('front.index') }}" class="hover:text-red-400 transition">{{ __('front::home.nav_home') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white font-medium">{{ __('front::home.faq_page_title') }}</span>
        </x-slot>
        <x-slot name="belowDivider">
            <p class="text-gray-300 mt-4 text-lg max-w-2xl leading-relaxed">
                {{ __('front::home.faq_page_intro') }}
            </p>
        </x-slot>
    </x-front::page-hero>

    <section class="bg-gray-50 py-12 md:py-16">
        <div class="container mx-auto px-5 lg:px-8">
            <div class="max-w-3xl mx-auto">
                @if ($faqs->isEmpty())
                    @include('front::includes.empty-state', ['text' => __('front::home.faq_empty')])
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

                <div class="mt-12 rounded-2xl bg-gradient-to-r from-red-50 to-orange-50 border border-red-100 p-6 md:p-8 text-center shadow-md">
                    <div class="flex flex-col items-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-red-600 shadow-inner">
                            <i class="fas fa-headset text-2xl" aria-hidden="true"></i>
                        </div>
                        <p class="text-gray-800 text-base md:text-lg font-medium">
                            {{ __('front::home.faq_still_questions') }}
                        </p>
                        <a href="{{ route('front.contact.show') }}"
                           class="inline-flex items-center gap-2 rounded-full bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-red-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <i class="fas fa-envelope" aria-hidden="true"></i>
                            {{ __('front::home.contact_us') }}
                            <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-0.5" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
