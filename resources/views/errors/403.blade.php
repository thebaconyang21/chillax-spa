@extends('layouts.app')

@section('title', 'Access Denied')
@section('breadcrumb', 'Access Denied')

@section('content')
    <div class="flex min-h-[calc(100vh-10rem)] items-center justify-center">
        <section class="w-full max-w-lg rounded-2xl border border-gold/40 bg-white p-8 text-center shadow-sm sm:p-10" aria-labelledby="forbidden-title">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-maroon/10 text-maroon">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>
            </div>
            <h1 id="forbidden-title" class="mt-5 font-display text-5xl text-maroon">Access Denied</h1>
            <p class="mt-3 text-sm leading-relaxed text-ink/65">Sorry, you do not have permission to access this part of Chillax Spa.</p>
            <a href="{{ route('dashboard') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-lg bg-gold px-5 py-3 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90">
                Back to Dashboard
            </a>
        </section>
    </div>
@endsection
