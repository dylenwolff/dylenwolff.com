@extends('layouts.site')

@section('title', 'About Me | '.$settings->name)
@section('description', 'Meet Dylen Andrew Wolff, an IT lecturer, systems engineer, digital solutions developer, and cybersecurity professional based in Colombo, Sri Lanka.')

@section('content')
<section class="site-container grid items-center gap-14 py-16 sm:py-24 lg:grid-cols-[.9fr_1.1fr]">
    <div class="relative mx-auto w-full max-w-md lg:mx-0">
        <div class="absolute -inset-5 rounded-[2.5rem] bg-gradient-to-br from-blue-500/25 via-cyan-400/10 to-transparent blur-2xl"></div>
        <div class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-100 shadow-2xl shadow-blue-950/15 dark:border-white/10 dark:bg-slate-900">
            @if($settings->profile_photo)
                <img src="{{ asset('storage/'.$settings->profile_photo) }}" alt="Portrait of {{ $settings->name }}" class="aspect-[4/5] w-full object-cover object-top">
            @else
                <div class="grid aspect-[4/5] place-items-center bg-gradient-to-br from-blue-600 to-cyan-500 text-8xl font-black text-white">DW</div>
            @endif
        </div>
        <div class="absolute -bottom-5 -right-3 rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-xl dark:border-white/10 dark:bg-[#0d1929]">
            <p class="text-xs font-bold uppercase tracking-[.16em] text-slate-400">Based in</p><p class="mt-1 font-bold">Colombo, Sri Lanka</p>
        </div>
    </div>
    <div><p class="section-kicker">A little about me</p><h1 class="mt-4 text-5xl font-black tracking-[-.05em] sm:text-6xl">I build technology around the problem, not the other way around.</h1><p class="mt-6 text-xl leading-9 text-slate-600 dark:text-slate-300">{{ $settings->name }} · {{ $settings->professional_title }}</p><div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('work') }}" class="button-primary">See what I have built</a><a href="{{ route('contact') }}" class="button-secondary">Start a conversation</a></div></div>
</section>

<section class="border-y border-slate-200/80 bg-slate-50/70 py-20 dark:border-white/10 dark:bg-white/[.025]"><div class="site-container grid gap-12 lg:grid-cols-[.65fr_1.35fr]"><div><p class="section-kicker">My perspective</p><h2 class="section-title mt-3">Useful, understandable, and built to last.</h2></div><div class="whitespace-pre-line text-lg leading-8 text-slate-600 dark:text-slate-300">{{ $settings->about_body }}</div></div></section>

<section class="site-container py-20 sm:py-28">
    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-4">
        <article class="service-card"><span class="service-number">01</span><h2>Software & systems</h2><p>Experience building, maintaining, and improving practical digital platforms and enterprise workflows.</p></article>
        <article class="service-card"><span class="service-number">02</span><h2>Cybersecurity</h2><p>An MSc in Cyber Security, with a particular interest in social engineering, online scams, and digital safety.</p></article>
        <article class="service-card"><span class="service-number">03</span><h2>Technology education</h2><p>Teaching has strengthened my ability to explain complex decisions clearly and guide people through unfamiliar technology.</p></article>
        <article class="service-card"><span class="service-number">04</span><h2>Community leadership</h2><p>Volunteer work has shaped an approach grounded in responsibility, empathy, and technology that serves real people.</p></article>
    </div>
</section>

@if($awards->isNotEmpty())
<section id="recognition" class="border-y border-slate-200/80 bg-slate-50/70 py-20 dark:border-white/10 dark:bg-white/[.025] sm:py-28"><div class="site-container"><div class="max-w-3xl"><p class="section-kicker">Awards & recognition</p><h2 class="section-title mt-3">Meaningful recognition for work that made a difference.</h2><p class="section-copy">These awards recognise personal contribution, digital transformation, and platforms created for the communities I serve.</p></div><div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">@foreach($awards as $award)<article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#0d1929]">@if($award->image_path)<div class="aspect-[4/3] overflow-hidden bg-slate-100 dark:bg-slate-900"><img src="{{ asset('storage/'.$award->image_path) }}" alt="{{ $award->title }}" class="h-full w-full object-cover" loading="lazy"></div>@endif<div class="p-6"><div class="flex items-center justify-between gap-4"><span class="font-bold text-blue-600 dark:text-blue-400">{{ $award->placement }}</span>@if($award->awarded_at)<time class="text-sm text-slate-400">{{ $award->awarded_at->format('F Y') }}</time>@endif</div><h3 class="mt-3 text-xl font-bold">{{ $award->title }}</h3><p class="mt-2 text-sm font-medium text-slate-500 dark:text-slate-400">{{ $award->issuer }}</p>@if($award->description)<p class="mt-4 leading-7 text-slate-600 dark:text-slate-300">{{ $award->description }}</p>@endif @if($award->proof_url)<a href="{{ $award->proof_url }}" target="_blank" rel="noreferrer" class="mt-5 inline-flex font-semibold text-blue-600 dark:text-blue-400">View recognition ↗</a>@endif</div></article>@endforeach</div></div></section>
@endif

<section class="site-container pb-20 sm:pb-28"><div class="overflow-hidden rounded-[2rem] bg-slate-950 px-7 py-12 text-white sm:px-12 sm:py-14"><div class="grid gap-10 md:grid-cols-[1fr_auto] md:items-center"><div><p class="font-mono text-sm font-semibold uppercase tracking-[.18em] text-blue-400">How I work</p><h2 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">Clear thinking before complicated technology.</h2><p class="mt-4 max-w-3xl text-lg leading-8 text-slate-300">I start by understanding the goal, the people involved, and what success should look like. Then I explain the options plainly and build the most practical path forward.</p></div><a href="{{ route('contact') }}" class="button-primary whitespace-nowrap">Let’s work together</a></div></div></section>
@endsection
