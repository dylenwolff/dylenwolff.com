<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Dylen Andrew Wolff builds practical digital solutions, reliable systems, and technology learning experiences.">
    <meta name="theme-color" content="#07111f">
    <title>{{ $settings->name }} — {{ $settings->professional_title }}</title>
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
            <a href="#top" class="group flex items-center gap-3" aria-label="Dylen Wolff home">
                <span class="grid size-10 place-items-center rounded-xl bg-blue-600 text-sm font-black tracking-tight text-white shadow-lg shadow-blue-600/20 transition-transform group-hover:-rotate-3">DW</span>
                <span class="font-semibold tracking-tight">{{ $settings->name }}</span>
            </a>
            <div class="hidden items-center gap-8 text-sm font-medium text-slate-600 md:flex dark:text-slate-300">
                <a class="nav-link" href="#about">About</a><a class="nav-link" href="#services">Services</a><a class="nav-link" href="#work">Work</a><a class="nav-link" href="#contact">Contact</a>
            </div>
            <div class="flex items-center gap-2">
                <button data-theme-toggle type="button" class="icon-button" aria-label="Switch color theme">
                    <svg class="size-5 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.66 6.34l1.41-1.41"/></svg>
                    <svg class="hidden size-5 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8Z"/></svg>
                </button>
                <button data-menu-toggle type="button" class="icon-button md:hidden" aria-expanded="false" aria-controls="mobile-menu" aria-label="Open navigation"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
                <a href="#contact" class="button-primary ml-2 hidden sm:inline-flex">Start a project</a>
            </div>
        </nav>
        <div id="mobile-menu" data-mobile-menu class="site-container hidden border-t border-slate-200 py-4 md:hidden dark:border-white/10">
            <div class="grid gap-1 text-sm font-medium"><a class="mobile-link" href="#about">About</a><a class="mobile-link" href="#services">Services</a><a class="mobile-link" href="#work">Work</a><a class="mobile-link" href="#contact">Contact</a></div>
        </div>
    </header>

    <main id="top">
        <section class="site-container grid min-h-[calc(100vh-5rem)] items-center gap-14 py-20 lg:grid-cols-[1.2fr_.8fr] lg:py-28">
            <div>
                <div class="eyebrow"><span class="size-2 rounded-full bg-emerald-500 shadow-[0_0_0_5px_rgba(16,185,129,.12)]"></span>{{ $settings->availability }}</div>
                <h1 class="mt-7 max-w-4xl text-5xl font-black leading-[.98] tracking-[-0.05em] sm:text-6xl lg:text-7xl">{{ $settings->hero_heading }}<br><span class="gradient-text">{{ $settings->hero_accent }}</span></h1>
                <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-600 sm:text-xl dark:text-slate-300">{{ $settings->hero_intro }}</p>
                <p class="mt-5 text-sm font-semibold uppercase tracking-[.13em] text-slate-500 dark:text-slate-400">{{ $settings->name }} · {{ $settings->professional_title }}</p>
                <div class="mt-9 flex flex-wrap gap-3"><a href="#work" class="button-primary">Explore my work <span aria-hidden="true">↗</span></a><a href="mailto:{{ $settings->email }}" class="button-secondary">{{ $settings->email }}</a></div>
                <div class="mt-10 flex flex-wrap items-center gap-x-7 gap-y-3 text-sm font-medium text-slate-500 dark:text-slate-400">
                    <a class="social-link" href="{{ $settings->linkedin_url }}" target="_blank" rel="noreferrer">LinkedIn <span>↗</span></a><a class="social-link" href="{{ $settings->github_url }}" target="_blank" rel="noreferrer">GitHub <span>↗</span></a><a class="social-link" href="{{ $settings->upwork_url }}" target="_blank" rel="noreferrer">Upwork <span>↗</span></a>
                </div>
            </div>
            <div class="relative mx-auto w-full max-w-lg lg:justify-self-end" aria-label="Professional focus overview">
                <div class="absolute -inset-5 rounded-[2rem] bg-gradient-to-br from-blue-500/20 via-cyan-400/5 to-transparent blur-2xl"></div>
                <div class="relative overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white/80 p-6 shadow-2xl shadow-blue-950/10 backdrop-blur dark:border-white/10 dark:bg-white/[.045] dark:shadow-black/30 sm:p-8">
                    <div class="flex items-center justify-between"><div class="flex gap-2" aria-hidden="true"><span class="size-2.5 rounded-full bg-rose-400"></span><span class="size-2.5 rounded-full bg-amber-400"></span><span class="size-2.5 rounded-full bg-emerald-400"></span></div><span class="text-xs font-bold uppercase tracking-[.16em] text-slate-400">What you can expect</span></div>
                    <div class="mt-8 text-sm leading-7"><p class="text-slate-400">A clear process</p><p class="mt-1 text-lg font-bold text-slate-800 dark:text-slate-100">From your idea to a useful result</p>
                        <div class="mt-5 grid gap-3"><div class="terminal-row"><span>01</span><strong>I listen and understand your goal</strong></div><div class="terminal-row"><span>02</span><strong>I explain the options in plain language</strong></div><div class="terminal-row"><span>03</span><strong>I build something practical and dependable</strong></div></div>
                        <p class="mt-6 text-slate-400">Current status</p><p class="mt-1 font-semibold text-emerald-500">● Ready to collaborate</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="border-y border-slate-200/80 bg-slate-50/70 py-24 dark:border-white/10 dark:bg-white/[.025]">
            <div class="site-container grid gap-12 lg:grid-cols-[.72fr_1.28fr]">
                <div><p class="section-kicker">About</p><h2 class="section-title mt-3">{{ $settings->about_heading }}</h2></div>
                <div class="grid gap-8 text-lg leading-8 text-slate-600 dark:text-slate-300"><div class="whitespace-pre-line">{{ $settings->about_body }}</div>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3"><div class="stat"><strong>Web</strong><span>Platforms & products</span></div><div class="stat"><strong>Systems</strong><span>Infrastructure & support</span></div><div class="stat col-span-2 sm:col-span-1"><strong>Education</strong><span>Teaching & training</span></div></div>
                </div>
            </div>
        </section>

        <section id="services" class="site-container py-24 sm:py-32">
            <div class="max-w-3xl"><p class="section-kicker">How I can help</p><h2 class="section-title mt-3">Digital solutions without the one-size-fits-all thinking.</h2><p class="section-copy">From a focused website to a complete internal platform, I select and combine technologies based on your actual goals.</p></div>
            <div class="mt-14 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $service)
                    <article class="service-card"><span class="service-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $service->title }}</h3><p>{{ $service->description }}</p>@if ($service->tags)<div class="tag-row">@foreach ($service->tags as $tag)<span>{{ $tag }}</span>@endforeach</div>@endif</article>
                @endforeach
                <article class="service-card service-card-accent"><span class="service-number">{{ str_pad($services->count() + 1, 2, '0', STR_PAD_LEFT) }}</span><h3>Something different?</h3><p>If it involves technology, let’s explore it together. The best solution starts with understanding what you need.</p><a href="#contact" class="mt-auto pt-6 font-semibold text-blue-600 dark:text-blue-400">Tell me about it →</a></article>
            </div>
        </section>

        <section id="work" class="border-y border-slate-200/80 bg-slate-950 py-24 text-white dark:border-white/10 sm:py-32">
            <div class="site-container">
                <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end"><div class="max-w-3xl"><p class="section-kicker text-blue-400">Selected work</p><h2 class="section-title mt-3 text-white">Work that connects technology with purpose.</h2></div><p class="max-w-sm text-slate-400">Real products and platforms designed, engineered and deployed from the ground up.</p></div>
                <div class="mt-14 grid gap-5 lg:grid-cols-2">
                    @foreach ($projects as $project)
                        <article class="project-card {{ $project->is_featured ? 'lg:col-span-2' : '' }}">
                            @if ($project->image_path)
                                <div class="relative aspect-[2/1] overflow-hidden border-b border-white/10 bg-slate-900"><img src="{{ $project->image_path }}" alt="{{ $project->title }} project preview" class="h-full w-full object-cover object-top" loading="lazy"><span class="absolute left-5 top-5 rounded-full bg-slate-950/80 px-3 py-1.5 font-mono text-xs font-bold uppercase tracking-[.16em] text-white backdrop-blur">{{ $project->category }}</span></div>
                            @else
                                <div class="project-visual {{ $loop->even ? 'project-visual-cyan' : 'project-visual-blue' }}"><span>{{ $project->category }}</span>@if ($loop->even)<div class="network-map"><i></i><i></i><i></i><i></i><i></i></div>@else<div class="mock-window"><div></div><div></div><div></div></div>@endif</div>
                            @endif
                            <div class="p-7 sm:p-9">
                                <div class="flex items-start justify-between gap-4"><div><p class="text-sm font-semibold text-blue-400">{{ $project->category }}</p><h3 class="mt-2 text-2xl font-bold sm:text-3xl">{{ $project->title }}</h3>@if ($project->client)<p class="mt-2 text-sm text-slate-500">{{ $project->client }}@if($project->status) · {{ $project->status }}@endif</p>@endif</div>@if ($project->url)<a href="{{ $project->url }}" target="_blank" rel="noreferrer" class="shrink-0 rounded-xl border border-white/10 px-4 py-2 text-sm font-bold text-white transition hover:border-blue-400 hover:text-blue-300">Visit project ↗</a>@endif</div>
                                <p class="mt-5 max-w-4xl text-lg leading-8 text-slate-300">{{ $project->summary }}</p>
                                @if ($project->is_featured && ($project->challenge || $project->solution || $project->responsibilities))
                                    <div class="mt-8 grid gap-7 border-t border-white/10 pt-8 md:grid-cols-3">
                                        @if ($project->challenge)<div><p class="font-mono text-xs font-bold uppercase tracking-[.16em] text-blue-400">The challenge</p><p class="mt-3 leading-7 text-slate-400">{{ $project->challenge }}</p></div>@endif
                                        @if ($project->solution)<div><p class="font-mono text-xs font-bold uppercase tracking-[.16em] text-blue-400">The solution</p><p class="mt-3 leading-7 text-slate-400">{{ $project->solution }}</p></div>@endif
                                        @if ($project->responsibilities)<div><p class="font-mono text-xs font-bold uppercase tracking-[.16em] text-blue-400">My role</p><p class="mt-3 leading-7 text-slate-400">{{ $project->responsibilities }}</p></div>@endif
                                    </div>
                                    @if ($project->technologies)<div class="mt-8 flex flex-wrap gap-2">@foreach ($project->technologies as $technology)<span class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5 font-mono text-xs text-slate-300">{{ $technology }}</span>@endforeach</div>@endif
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="contact" class="site-container py-24 sm:py-32">
            <div class="relative overflow-hidden rounded-[2rem] bg-blue-600 px-6 py-12 text-white shadow-2xl shadow-blue-600/20 sm:px-12 lg:px-16 lg:py-16">
                <div class="absolute -right-24 -top-24 size-80 rounded-full border-[60px] border-white/5" aria-hidden="true"></div>
                <div class="relative grid gap-12 lg:grid-cols-[.85fr_1.15fr] lg:items-start">
                    <div class="max-w-xl"><p class="font-mono text-sm font-semibold uppercase tracking-[.18em] text-blue-100">Start a conversation</p><h2 class="mt-4 text-4xl font-black tracking-[-.04em] sm:text-5xl">{{ $settings->contact_heading }}</h2><p class="mt-5 text-lg leading-8 text-blue-100">{{ $settings->contact_body }}</p><a href="mailto:{{ $settings->email }}" class="mt-7 inline-flex font-semibold text-white underline decoration-blue-300 underline-offset-4">{{ $settings->email }}</a></div>
                    <form method="POST" action="{{ route('contact.store') }}" class="grid gap-5 rounded-2xl bg-white p-6 text-slate-950 shadow-xl sm:p-8">
                        @csrf
                        <div class="hidden" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
                        @if (session('contact_success'))<div class="rounded-xl bg-emerald-50 px-4 py-3 font-semibold text-emerald-800">{{ session('contact_success') }}</div>@endif
                        @if ($errors->any())<div class="rounded-xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">Please check the highlighted fields and try again.</div>@endif
                        <div class="grid gap-5 sm:grid-cols-2">
                            <label class="contact-label">Your name<input class="contact-input" name="name" value="{{ old('name') }}" required autocomplete="name">@error('name')<span class="text-xs text-rose-600">{{ $message }}</span>@enderror</label>
                            <label class="contact-label">Email address<input class="contact-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">@error('email')<span class="text-xs text-rose-600">{{ $message }}</span>@enderror</label>
                        </div>
                        <label class="contact-label">What can I help with?<input class="contact-input" name="subject" value="{{ old('subject') }}" required placeholder="A website, business tool, training…">@error('subject')<span class="text-xs text-rose-600">{{ $message }}</span>@enderror</label>
                        <label class="contact-label">Tell me a little about it<textarea class="contact-input min-h-32 resize-y" name="message" required placeholder="Your idea, problem, or goal">{{ old('message') }}</textarea>@error('message')<span class="text-xs text-rose-600">{{ $message }}</span>@enderror</label>
                        <button class="inline-flex min-h-14 items-center justify-center rounded-xl bg-slate-950 px-7 font-bold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-blue-700" type="submit">Send enquiry →</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 py-10 dark:border-white/10"><div class="site-container flex flex-col gap-5 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between dark:text-slate-400"><p>© {{ date('Y') }} {{ $settings->name }}. Built with purpose.</p><div class="flex gap-5"><a class="hover:text-blue-500" href="{{ $settings->linkedin_url }}" target="_blank" rel="noreferrer">LinkedIn</a><a class="hover:text-blue-500" href="{{ $settings->github_url }}" target="_blank" rel="noreferrer">GitHub</a><a class="hover:text-blue-500" href="mailto:{{ $settings->email }}">Email</a></div></div></footer>
</body>
</html>
