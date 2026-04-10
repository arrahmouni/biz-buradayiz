@extends('front::layouts.master')

@section('content')
    <article class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 py-12 md:py-16">
            <header class="max-w-3xl mx-auto mb-10">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">
                    {{ $page->smartTrans('title') }}
                </h1>
            </header>
            <div class="max-w-3xl mx-auto text-gray-700 leading-relaxed space-y-4 cms-page-body [&_h2]:text-xl [&_h2]:font-semibold [&_h2]:mt-8 [&_h2]:mb-3 [&_h3]:text-lg [&_h3]:font-semibold [&_h3]:mt-6 [&_p]:mb-4 [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:list-decimal [&_ol]:pl-6 [&_a]:text-red-600 [&_a]:underline hover:[&_a]:text-red-700">
                {!! $page->smartTrans('long_description') !!}
            </div>
        </div>
    </article>
@endsection
