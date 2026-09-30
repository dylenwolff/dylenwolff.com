<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Dylen Andrew Wolff builds practical digital solutions, reliable systems, and technology learning experiences.">
    <meta name="theme-color" content="#07111f">
    <title>Dylen Andrew Wolff — Systems Engineer & Digital Solutions Developer</title>
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
                <span class="font-semibold tracking-tight">Dylen Wolff</span>
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
                <div class="eyebrow"><span class="size-2 rounded-full bg-emerald-500 shadow-[0_0_0_5px_rgba(16,185,129,.12)]"></span>Available for selected projects</div>
                <h1 class="mt-7 max-w-4xl text-5xl font-black leading-[.98] tracking-[-0.05em] sm:text-6xl lg:text-7xl">Practical technology.<br><span class="gradient-text">Built around people.</span></h1>
                <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-600 sm:text-xl dark:text-slate-300">I’m <strong class="font-semibold text-slate-950 dark:text-white">Dylen Andrew Wolff</strong>, a Systems Engineer and Digital Solutions Developer. I design reliable web platforms, business systems, infrastructure, and learning experiences shaped around what people actually need.</p>
                <div class="mt-9 flex flex-wrap gap-3"><a href="#work" class="button-primary">Explore my work <span aria-hidden="true">↗</span></a><a href="mailto:hello@dylenwolff.com" class="button-secondary">hello@dylenwolff.com</a></div>
                <div class="mt-10 flex flex-wrap items-center gap-x-7 gap-y-3 text-sm font-medium text-slate-500 dark:text-slate-400">
                    <a class="social-link" href="https://www.linkedin.com/in/dylenaw/" target="_blank" rel="noreferrer">LinkedIn <span>↗</span></a><a class="social-link" href="https://github.com/dylenwolff" target="_blank" rel="noreferrer">GitHub <span>↗</span></a><a class="social-link" href="https://www.upwork.com/freelancers/~01d1b15fc05390f9a2" target="_blank" rel="noreferrer">Upwork <span>↗</span></a>
                </div>
            </div>
            <div class="relative mx-auto w-full max-w-lg lg:justify-self-end" aria-label="Professional focus overview">
                <div class="absolute -inset-5 rounded-[2rem] bg-gradient-to-br from-blue-500/20 via-cyan-400/5 to-transparent blur-2xl"></div>
                <div class="relative overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white/80 p-6 shadow-2xl shadow-blue-950/10 backdrop-blur dark:border-white/10 dark:bg-white/[.045] dark:shadow-black/30 sm:p-8">
                    <div class="flex items-center justify-between"><div class="flex gap-2" aria-hidden="true"><span class="size-2.5 rounded-full bg-rose-400"></span><span class="size-2.5 rounded-full bg-amber-400"></span><span class="size-2.5 rounded-full bg-emerald-400"></span></div><span class="font-mono text-xs text-slate-400">dylen@work</span></div>
                    <div class="mt-8 font-mono text-sm leading-7"><p class="text-slate-400"><span class="text-blue-500">$</span> current_focus</p><p class="mt-1 text-slate-800 dark:text-slate-200">Building dependable digital products</p><p class="mt-5 text-slate-400"><span class="text-blue-500">$</span> approach --list</p>
                        <div class="mt-2 grid gap-3"><div class="terminal-row"><span>01</span><strong>Understand the real problem</strong></div><div class="terminal-row"><span>02</span><strong>Choose the right technology</strong></div><div class="terminal-row"><span>03</span><strong>Build for long-term value</strong></div></div>
                        <p class="mt-6 text-slate-400"><span class="text-blue-500">$</span> status</p><p class="mt-1 text-emerald-500">● ready_to_collaborate</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="border-y border-slate-200/80 bg-slate-50/70 py-24 dark:border-white/10 dark:bg-white/[.025]">
            <div class="site-container grid gap-12 lg:grid-cols-[.72fr_1.28fr]">
                <div><p class="section-kicker">About</p><h2 class="section-title mt-3">Technology is useful when it solves something real.</h2></div>
                <div class="grid gap-8 text-lg leading-8 text-slate-600 dark:text-slate-300"><p>I work across development, systems, infrastructure, and education. That range lets me see beyond a single tool and create solutions that fit the client, the team, and the problem—not the other way around.</p><p>As an IT lecturer, I also care deeply about making complex ideas understandable. The same clarity shapes how I communicate, document, and deliver every project.</p>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3"><div class="stat"><strong>Web</strong><span>Platforms & products</span></div><div class="stat"><strong>Systems</strong><span>Infrastructure & support</span></div><div class="stat col-span-2 sm:col-span-1"><strong>Education</strong><span>Teaching & training</span></div></div>
                </div>
            </div>
        </section>

        <section id="services" class="site-container py-24 sm:py-32">
            <div class="max-w-3xl"><p class="section-kicker">How I can help</p><h2 class="section-title mt-3">Digital solutions without the one-size-fits-all thinking.</h2><p class="section-copy">From a focused website to a complete internal platform, I select and combine technologies based on your actual goals.</p></div>
            <div class="mt-14 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                <article class="service-card"><span class="service-number">01</span><h3>Web platforms</h3><p>Fast, responsive websites, portals, and web applications designed to support a real business outcome.</p><div class="tag-row"><span>Laravel</span><span>CMS</span><span>APIs</span></div></article>
                <article class="service-card"><span class="service-number">02</span><h3>Business systems</h3><p>Purpose-built tools that streamline operations, connect information, and replace inefficient manual work.</p><div class="tag-row"><span>Automation</span><span>Dashboards</span><span>Data</span></div></article>
                <article class="service-card"><span class="service-number">03</span><h3>Infrastructure</h3><p>Practical server, deployment, hosting, networking, and reliability support for growing digital products.</p><div class="tag-row"><span>Linux</span><span>Cloud</span><span>DevOps</span></div></article>
                <article class="service-card"><span class="service-number">04</span><h3>Technical consulting</h3><p>Clear guidance on architecture, technology choices, modernization, and solving difficult technical problems.</p><div class="tag-row"><span>Strategy</span><span>Architecture</span></div></article>
                <article class="service-card"><span class="service-number">05</span><h3>Training & education</h3><p>Accessible technical training, curriculum development, mentoring, and knowledge transfer for teams and learners.</p><div class="tag-row"><span>Teaching</span><span>Workshops</span></div></article>
                <article class="service-card service-card-accent"><span class="service-number">06</span><h3>Something different?</h3><p>If it involves technology, let’s explore it. I’m comfortable learning the right tools for an unusual challenge.</p><a href="#contact" class="mt-auto pt-6 font-semibold text-blue-600 dark:text-blue-400">Tell me about it →</a></article>
            </div>
        </section>

        <section id="work" class="border-y border-slate-200/80 bg-slate-950 py-24 text-white dark:border-white/10 sm:py-32">
            <div class="site-container">
                <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end"><div class="max-w-3xl"><p class="section-kicker text-blue-400">Selected work</p><h2 class="section-title mt-3 text-white">Work that connects technology with purpose.</h2></div><p class="max-w-sm text-slate-400">Detailed case studies are being prepared. Client-sensitive work remains private.</p></div>
                <div class="mt-14 grid gap-5 lg:grid-cols-2">
                    <article class="project-card"><div class="project-visual project-visual-blue"><span>WEB PLATFORM</span><div class="mock-window"><div></div><div></div><div></div></div></div><div class="p-7"><div class="flex items-start justify-between gap-4"><div><p class="text-sm text-blue-400">Design & Development</p><h3 class="mt-2 text-2xl font-bold">Digital platforms that do more</h3></div><span class="text-2xl text-slate-500">↗</span></div><p class="mt-4 leading-7 text-slate-400">Custom experiences engineered around the client’s workflow, audience, and long-term plans.</p></div></article>
                    <article class="project-card"><div class="project-visual project-visual-cyan"><span>SYSTEMS</span><div class="network-map"><i></i><i></i><i></i><i></i><i></i></div></div><div class="p-7"><div class="flex items-start justify-between gap-4"><div><p class="text-sm text-cyan-400">Infrastructure & Operations</p><h3 class="mt-2 text-2xl font-bold">Reliable systems behind the screen</h3></div><span class="text-2xl text-slate-500">↗</span></div><p class="mt-4 leading-7 text-slate-400">Hosting, deployment, integrations, and operational improvements designed to keep work moving.</p></div></article>
                </div>
            </div>
        </section>

        <section id="contact" class="site-container py-24 sm:py-32">
            <div class="relative overflow-hidden rounded-[2rem] bg-blue-600 px-6 py-16 text-white shadow-2xl shadow-blue-600/20 sm:px-12 lg:px-16"><div class="absolute -right-24 -top-24 size-80 rounded-full border-[60px] border-white/5" aria-hidden="true"></div><div class="relative grid gap-10 lg:grid-cols-[1fr_auto] lg:items-end"><div class="max-w-3xl"><p class="font-mono text-sm font-semibold uppercase tracking-[.18em] text-blue-100">Start a conversation</p><h2 class="mt-4 text-4xl font-black tracking-[-.04em] sm:text-5xl">Have an idea, a problem, or a project?</h2><p class="mt-5 max-w-2xl text-lg leading-8 text-blue-100">Tell me what you’re trying to accomplish. I’ll help you find a practical way forward—even if the answer is simpler than expected.</p></div><a href="mailto:hello@dylenwolff.com?subject=Project%20enquiry" class="inline-flex min-h-14 items-center justify-center rounded-xl bg-white px-7 font-bold text-blue-700 shadow-xl transition hover:-translate-y-0.5 hover:bg-blue-50">Email me ↗</a></div></div>
        </section>
    </main>

    <footer class="border-t border-slate-200 py-10 dark:border-white/10"><div class="site-container flex flex-col gap-5 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between dark:text-slate-400"><p>© {{ date('Y') }} Dylen Andrew Wolff. Built with purpose.</p><div class="flex gap-5"><a class="hover:text-blue-500" href="https://www.linkedin.com/in/dylenaw/" target="_blank" rel="noreferrer">LinkedIn</a><a class="hover:text-blue-500" href="https://github.com/dylenwolff" target="_blank" rel="noreferrer">GitHub</a><a class="hover:text-blue-500" href="mailto:hello@dylenwolff.com">Email</a></div></div></footer>
</body>
</html>
