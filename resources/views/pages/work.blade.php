@extends('layouts.site')

@section('title', 'Selected Work — '.$settings->name)
@section('description', 'Explore digital platforms and purpose-built systems designed and developed by '.$settings->name.'.')

@section('content')
<section class="site-container py-20 sm:py-28">
    <div class="max-w-3xl"><p class="section-kicker">Selected work</p><h1 class="mt-4 text-5xl font-black tracking-[-.05em] sm:text-6xl">Useful technology, built around real people.</h1><p class="section-copy">A selection of platforms, products, and public-interest projects I have designed and engineered from the ground up.</p></div>
</section>
<section class="border-y border-slate-200/80 bg-slate-950 py-20 text-white dark:border-white/10 sm:py-28">
    <div class="site-container grid gap-6 md:grid-cols-2">
        @foreach ($projects as $project)
            <article class="project-card overflow-hidden">
                @if ($project->image_path)<a href="{{ route('work.show', $project) }}" class="block aspect-[16/9] overflow-hidden border-b border-white/10 bg-slate-900"><img src="{{ str_starts_with($project->image_path, '/') || str_starts_with($project->image_path, 'http') ? $project->image_path : asset('storage/'.$project->image_path) }}" alt="{{ $project->title }} preview" class="h-full w-full object-cover object-top transition duration-500 hover:scale-[1.02]" loading="lazy"></a>@endif
                <div class="p-7 sm:p-8"><p class="text-sm font-semibold text-blue-400">{{ $project->category }}</p><h2 class="mt-2 text-2xl font-bold">{{ $project->title }}</h2>@if($project->client)<p class="mt-2 text-sm text-slate-500">{{ $project->client }}@if($project->status) · {{ $project->status }}@endif</p>@endif<p class="mt-4 leading-7 text-slate-300">{{ $project->summary }}</p><a href="{{ route('work.show', $project) }}" class="mt-6 inline-flex font-semibold text-blue-400 hover:text-blue-300">Read the case study →</a></div>
            </article>
        @endforeach
    </div>
</section>
<section class="site-container py-20 text-center sm:py-24"><h2 class="text-3xl font-black tracking-tight sm:text-4xl">Have a problem worth solving?</h2><p class="mx-auto mt-4 max-w-2xl text-lg text-slate-600 dark:text-slate-300">Tell me what you are trying to achieve. We can work out the technology together.</p><a href="{{ route('contact') }}" class="button-primary mt-8">Start a conversation</a></section>
@endsection
