@extends('front::layouts.master')

@section('content')
    <div class="relative overflow-hidden bg-gradient-to-b from-slate-50 via-white to-gray-50 pb-20 pt-8 md:pt-14">
        <div class="pointer-events-none absolute -left-24 top-12 h-80 w-80 rounded-full bg-red-500/15 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -right-16 bottom-8 h-72 w-72 rounded-full bg-amber-400/15 blur-3xl" aria-hidden="true"></div>
        <div class="container relative z-[1] mx-auto px-5 lg:px-8">
            <div class="mx-auto max-w-xl rounded-3xl border border-gray-200/90 bg-white p-8 shadow-xl shadow-gray-300/30 ring-1 ring-black/5 md:p-12">
                @yield('error_body')
            </div>
        </div>
    </div>
@endsection
