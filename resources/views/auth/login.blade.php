<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Log In - Chillax Spa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Nunito+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-maroon font-sans text-ink antialiased">
    <main class="relative flex min-h-screen items-start justify-center overflow-hidden bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-gold/10 via-maroon to-maroon px-4 py-10 sm:px-6">
        <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full border border-gold/10" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-36 -right-24 h-96 w-96 rounded-full border border-gold/10" aria-hidden="true"></div>

        <section class="relative my-auto w-full max-w-md rounded-3xl border border-gold/45 bg-gold/10 p-1.5 shadow-2xl shadow-black/25 backdrop-blur-sm">
            <div class="rounded-[1.35rem] border border-gold/25 bg-cream/95 px-6 py-9 shadow-inner sm:px-10 sm:py-11">
                <header class="mb-8 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full border border-gold/50 bg-gold/10 text-maroon">
                        <svg class="h-8 w-8" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M16 27c-7-5-11-10-10-16 5 0 9 2 10 8 1-8 5-12 11-13 1 7-2 12-9 15"/>
                            <path d="M16 27V12M9 22c2 2 4 3 7 5M23 20c-2 2-4 3-7 5"/>
                        </svg>
                    </div>
                    <h1 class="brand-wordmark text-6xl leading-none text-maroon">Chillax Spa</h1>
                    <p class="mt-3 text-[10px] font-medium uppercase tracking-[0.22em] text-maroon/65">Relax &bull; Refresh &bull; Rejuvenate</p>
                    <div class="mx-auto mt-6 h-px w-16 bg-gold/70" aria-hidden="true"></div>
                    <h2 class="mt-6 text-lg font-semibold text-ink">Welcome Back</h2>
                    <p class="mt-1 text-sm text-ink/60">Sign in to your spa management account</p>
                </header>

                <form method="POST" action="{{ route('login') }}" aria-label="Log in">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label for="username" class="mb-2 block text-sm font-medium text-ink">Username</label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-maroon/55" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
                                <input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" placeholder="Enter your username" class="h-12 w-full rounded-xl border border-maroon/15 bg-white pl-11 pr-4 text-sm text-ink outline-none transition placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
                            </div>
                            @error('username')
                                <p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-medium text-ink">Password</label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-maroon/55" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 1 1 8 0v3M12 14v3"/></svg>
                                <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" class="h-12 w-full rounded-xl border border-maroon/15 bg-white pl-11 pr-12 text-sm text-ink outline-none transition placeholder:text-ink/40 focus:border-gold focus:ring-2 focus:ring-gold/25">
                                <button id="password-toggle" type="button" class="absolute inset-y-0 right-0 inline-flex w-11 items-center justify-center rounded-r-xl text-maroon/60 transition hover:text-maroon focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gold" aria-label="Show password" aria-pressed="false">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="mt-7 inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gold px-5 text-sm font-semibold text-maroon shadow-md shadow-gold/20 transition hover:bg-gold/90 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2 focus:ring-offset-cream">
                        Log In
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                </form>

                <p class="mt-8 text-center text-xs text-ink/45">&copy; {{ now()->year }} Chillax Spa. All rights reserved.</p>
            </div>
        </section>
    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('password-toggle');

        passwordToggle.addEventListener('click', () => {
            const isPasswordVisible = passwordInput.type === 'text';

            passwordInput.type = isPasswordVisible ? 'password' : 'text';
            passwordToggle.setAttribute('aria-pressed', String(!isPasswordVisible));
            passwordToggle.setAttribute('aria-label', isPasswordVisible ? 'Show password' : 'Hide password');
        });
    </script>
</body>
</html>
