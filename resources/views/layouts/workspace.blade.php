<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · Dylen Wolff</title>
    <script>document.documentElement.dataset.theme=localStorage.getItem('theme')||((matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light')</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 text-base-content">
@php
    $sections = [
        'Sales' => [
            ['label' => 'Messages', 'slug' => 'contact-messages', 'icon' => 'mail'],
            ['label' => 'Organizations', 'slug' => 'organizations', 'icon' => 'building-2'],
            ['label' => 'Contacts', 'slug' => 'contacts', 'icon' => 'contact'],
            ['label' => 'Leads', 'slug' => 'leads', 'icon' => 'radar'],
            ['label' => 'Opportunities', 'slug' => 'opportunities', 'icon' => 'badge-dollar-sign'],
            ['label' => 'Email queue', 'slug' => 'outreach-emails', 'icon' => 'send'],
        ],
        'Work' => [
            ['label' => 'Tasks', 'slug' => 'crm-tasks', 'icon' => 'list-checks'],
            ['label' => 'Activities', 'slug' => 'activities', 'icon' => 'history'],
            ['label' => 'Client projects', 'slug' => 'client-projects', 'icon' => 'briefcase-business'],
        ],
        'Clients' => [
            ['label' => 'Support', 'slug' => 'support-tickets', 'icon' => 'life-buoy'],
            ['label' => 'Documents', 'slug' => 'client-documents', 'icon' => 'folder-lock'],
        ],
        'Billing' => [
            ['label' => 'Quotes', 'slug' => 'quotes', 'icon' => 'file-text'],
            ['label' => 'Invoices', 'slug' => 'invoices', 'icon' => 'receipt-text'],
            ['label' => 'Maintenance', 'slug' => 'maintenance-plans', 'icon' => 'refresh-cw'],
        ],
        'Website' => [
            ['label' => 'Projects', 'slug' => 'projects', 'icon' => 'panels-top-left'],
            ['label' => 'Services', 'slug' => 'services', 'icon' => 'blocks'],
            ['label' => 'Awards', 'slug' => 'awards', 'icon' => 'award'],
            ['label' => 'Qualifications', 'slug' => 'qualifications', 'icon' => 'graduation-cap'],
            ['label' => 'Site content', 'slug' => 'site-settings', 'icon' => 'settings-2'],
        ],
    ];
@endphp

<div class="drawer lg:drawer-open">
    <input id="workspace-drawer" type="checkbox" class="drawer-toggle">
    <div class="drawer-content min-w-0">
        <header class="navbar sticky top-0 z-30 border-b border-base-300 bg-base-100/90 px-4 backdrop-blur lg:px-8">
            <div class="navbar-start gap-2">
                <label for="workspace-drawer" class="btn btn-square btn-ghost lg:hidden" aria-label="Open navigation"><i data-lucide="menu" class="size-5"></i></label>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.16em] text-primary">Business hub</p>
                    <h1 class="text-lg font-bold leading-tight">@yield('heading', 'Dashboard')</h1>
                </div>
            </div>
            <div class="navbar-end gap-2">
                <a href="https://dylenwolff.com" class="btn btn-ghost btn-sm hidden sm:inline-flex" target="_blank"><i data-lucide="external-link" class="size-4"></i>Website</a>
                <button class="btn btn-square btn-ghost btn-sm" data-workspace-theme aria-label="Change theme"><i data-lucide="sun-moon" class="size-4"></i></button>
            </div>
        </header>

        <main class="p-4 lg:p-8">
            @yield('content')
        </main>
    </div>

    <aside class="drawer-side z-40">
        <label for="workspace-drawer" aria-label="Close navigation" class="drawer-overlay"></label>
        <div class="flex min-h-full w-72 flex-col border-r border-base-300 bg-base-100">
            <a href="{{ route('workspace.dashboard') }}" class="flex h-20 items-center gap-3 border-b border-base-300 px-5">
                <span class="grid size-10 place-items-center rounded-xl bg-primary text-primary-content"><i data-lucide="orbit" class="size-5"></i></span>
                <span><strong class="block leading-tight">Dylen Wolff</strong><span class="text-xs text-base-content/55">Business workspace</span></span>
            </a>
            <nav class="menu w-full flex-1 flex-nowrap overflow-y-auto p-3">
                <li class="w-full"><a href="{{ route('workspace.dashboard') }}" class="w-full {{ request()->routeIs('workspace.dashboard', 'workspace.home') ? 'active' : '' }}"><i data-lucide="layout-dashboard" class="size-4"></i>Dashboard</a></li>
                @foreach($sections as $group => $items)
                    <li class="menu-title mt-3">{{ $group }}</li>
                    @foreach($items as $item)
                        <li class="w-full"><a href="{{ route('workspace.manage', $item['slug']) }}" class="w-full {{ request()->route('section') === $item['slug'] ? 'active' : '' }}"><i data-lucide="{{ $item['icon'] }}" class="size-4"></i>{{ $item['label'] }}</a></li>
                    @endforeach
                @endforeach
            </nav>
            <div class="border-t border-base-300 p-3">
                <div class="flex items-center gap-3 rounded-box px-2 py-2">
                    <span class="avatar"><span class="size-10 overflow-hidden rounded-full border border-base-300 bg-base-200 p-1"><img src="{{ asset('images/brand/dylen-wolff-mark.svg') }}" alt="Dylen Wolff"></span></span>
                    <span class="min-w-0 flex-1"><strong class="block truncate text-sm">{{ auth()->user()->name }}</strong><span class="block truncate text-xs text-base-content/50">{{ auth()->user()->email }}</span></span>
                    <form method="POST" action="{{ route('workspace.logout') }}">@csrf<button class="btn btn-square btn-ghost btn-sm" aria-label="Sign out" title="Sign out"><i data-lucide="log-out" class="size-4"></i></button></form>
                </div>
            </div>
        </div>
    </aside>
</div>
</body>
</html>
