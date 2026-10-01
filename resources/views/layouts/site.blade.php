<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', 'Dylen Andrew Wolff builds practical digital solutions, reliable systems, and technology learning experiences.')">
    <meta name="theme-color" content="#07111f">
    <link rel="icon" href="{{ asset('images/brand/favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('images/brand/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <title>@yield('title', $settings->name.' | '.$settings->professional_title)</title>
    <script>
        (() => {
            const saved = localStorage.getItem('theme');
            const dark = saved ? saved === 'dark' : matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white text-slate-950 antialiased transition-colors duration-300 dark:bg-[#07111f] dark:text-slate-100">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute left-1/2 top-[-18rem] h-[36rem] w-[52rem] -translate-x-1/2 rounded-full bg-blue-500/10 blur-[110px] dark:bg-blue-500/15"></div>
        <div class="grid-fade absolute inset-0 opacity-60 dark:opacity-30"></div>
    </div>

    <header class="sticky top-0 z-50 border-b border-slate-200/70 bg-white/80 backdrop-blur-xl dark:border-white/10 dark:bg-[#07111f]/80">
        <nav class="site-container flex h-20 items-center justify-between" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="group flex items-center gap-3" aria-label="Dylen Wolff home">
                <span class="grid size-10 place-items-center rounded-xl bg-white p-2 shadow-lg shadow-blue-600/15 transition-transform group-hover:rotate-6 dark:bg-white/5"><img src="{{ asset('images/brand/dylen-wolff-mark.svg') }}" alt="" class="size-full dark:hidden"><img src="{{ asset('images/brand/dylen-wolff-mark-dark.svg') }}" alt="" class="hidden size-full dark:block"></span>
                <span class="font-semibold tracking-tight">{{ $settings->name }}</span>
            </a>
            <div class="hidden items-center gap-6 text-sm font-medium text-slate-600 md:flex dark:text-slate-300">
                <a class="nav-link {{ request()->routeIs('home') ? 'text-blue-600 dark:text-blue-400' : '' }}" href="{{ route('home') }}">Home</a><a class="nav-link {{ request()->routeIs('about') ? 'text-blue-600 dark:text-blue-400' : '' }}" href="{{ route('about') }}">About Me</a><a class="nav-link {{ request()->routeIs('services') ? 'text-blue-600 dark:text-blue-400' : '' }}" href="{{ route('services') }}">What I Do</a><a class="nav-link {{ request()->routeIs('work*') ? 'text-blue-600 dark:text-blue-400' : '' }}" href="{{ route('work') }}">My Work</a><a class="nav-link {{ request()->routeIs('contact*') ? 'text-blue-600 dark:text-blue-400' : '' }}" href="{{ route('contact') }}">Let’s Talk</a>
            </div>
            <div class="flex items-center gap-2">
                <button data-theme-toggle type="button" class="icon-button" aria-label="Switch color theme">
                    <svg class="size-5 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.66 6.34l1.41-1.41"/></svg>
                    <svg class="hidden size-5 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8Z"/></svg>
                </button>
                <button data-menu-toggle type="button" class="icon-button md:hidden" aria-expanded="false" aria-controls="mobile-menu" aria-label="Open navigation"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
                <a href="{{ route('contact') }}" class="button-primary ml-2 hidden sm:inline-flex">Start a project</a>
            </div>
        </nav>
        <div id="mobile-menu" data-mobile-menu class="site-container hidden border-t border-slate-200 py-4 md:hidden dark:border-white/10">
            <div class="grid gap-1 text-sm font-medium"><a class="mobile-link" href="{{ route('home') }}">Home</a><a class="mobile-link" href="{{ route('about') }}">About Me</a><a class="mobile-link" href="{{ route('services') }}">What I Do</a><a class="mobile-link" href="{{ route('work') }}">My Work</a><a class="mobile-link" href="{{ route('contact') }}">Let’s Talk</a></div>
        </div>
    </header>

    <main>@yield('content')</main>

    <footer class="border-t border-slate-200 py-10 dark:border-white/10"><div class="site-container flex flex-col gap-5 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between dark:text-slate-400"><p>© {{ date('Y') }} {{ $settings->name }}. Built with purpose.</p><div class="flex gap-5"><a class="hover:text-blue-500" href="{{ $settings->linkedin_url }}" target="_blank" rel="noreferrer">LinkedIn</a><a class="hover:text-blue-500" href="{{ $settings->github_url }}" target="_blank" rel="noreferrer">GitHub</a><a class="hover:text-blue-500" href="mailto:{{ $settings->email }}">Email</a></div></div></footer>
</body>
</html>
