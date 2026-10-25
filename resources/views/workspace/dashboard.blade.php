@extends('layouts.workspace')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<div class="mx-auto max-w-[1600px] space-y-6">
    <section class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm text-base-content/60">{{ now('Asia/Colombo')->format('l, d F Y') }}</p>
            <h2 class="mt-1 text-2xl font-black tracking-tight sm:text-3xl">Good {{ now('Asia/Colombo')->hour < 12 ? 'morning' : (now('Asia/Colombo')->hour < 17 ? 'afternoon' : 'evening') }}, Dylen.</h2>
            <p class="mt-2 max-w-2xl text-base-content/60">Your leads, work and client activity in one place.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('workspace.manage', 'leads') }}" class="btn btn-primary"><i data-lucide="plus" class="size-4"></i>Add lead</a>
            <a href="{{ route('workspace.manage', 'crm-tasks') }}" class="btn btn-outline"><i data-lucide="list-checks" class="size-4"></i>View tasks</a>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['Open leads', $stats['openLeads'], 'radar', 'text-primary', 'Sales prospects in progress'],
            ['Pipeline value', '$'.number_format($stats['pipelineValue']), 'chart-no-axes-combined', 'text-success', 'Open opportunity value'],
            ['Tasks due', $stats['tasksDue'], 'clock-alert', 'text-warning', 'Due today or overdue'],
            ['Unread messages', $stats['unreadMessages'], 'mail', 'text-info', 'New website enquiries'],
        ] as [$label, $value, $icon, $color, $description])
            <article class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body gap-3 p-5">
                    <div class="flex items-start justify-between"><span class="text-sm font-semibold text-base-content/60">{{ $label }}</span><i data-lucide="{{ $icon }}" class="size-5 {{ $color }}"></i></div>
                    <strong class="text-3xl font-black tracking-tight">{{ $value }}</strong>
                    <span class="text-xs text-base-content/50">{{ $description }}</span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(360px,.8fr)]">
        <article class="card border border-base-300 bg-base-100 shadow-sm">
            <div class="card-body p-0">
                <div class="flex items-center justify-between gap-3 border-b border-base-300 p-5">
                    <div><h3 class="card-title text-lg">Recent leads</h3><p class="text-sm text-base-content/55">Latest prospects added to the CRM</p></div>
                    <a href="{{ route('workspace.manage', 'leads') }}" class="btn btn-ghost btn-sm">View all<i data-lucide="arrow-right" class="size-4"></i></a>
                </div>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead><tr><th>Lead</th><th>Organisation</th><th>Status</th><th>Value</th><th>Follow-up</th></tr></thead>
                        <tbody>
                        @forelse($recentLeads as $lead)
                            <tr>
                                <td><div class="max-w-72 font-semibold">{{ $lead->title }}</div><div class="text-xs text-base-content/45">{{ $lead->source }}</div></td>
                                <td>{{ $lead->organization?->name ?? $lead->contact?->full_name ?? 'Unassigned' }}</td>
                                <td><span class="badge badge-sm {{ $lead->status === 'contacted' ? 'badge-info' : ($lead->status === 'qualified' ? 'badge-success' : 'badge-ghost') }}">{{ str($lead->status)->headline() }}</span></td>
                                <td class="font-mono text-sm">{{ $lead->estimated_value ? '$'.number_format((float)$lead->estimated_value) : '—' }}</td>
                                <td class="whitespace-nowrap text-sm">{{ $lead->next_follow_up_at?->timezone('Asia/Colombo')->format('d M') ?? 'Not set' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-12 text-center text-base-content/50">No leads yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </article>

        <article class="card border border-base-300 bg-base-100 shadow-sm">
            <div class="card-body p-0">
                <div class="flex items-center justify-between border-b border-base-300 p-5">
                    <div><h3 class="card-title text-lg">Next actions</h3><p class="text-sm text-base-content/55">What needs your attention</p></div>
                    <span class="badge badge-warning badge-outline">{{ $stats['tasksDue'] }} due</span>
                </div>
                <div class="divide-y divide-base-300">
                    @forelse($dueTasks as $task)
                        <div class="flex gap-3 p-4">
                            <span class="mt-1 size-2 shrink-0 rounded-full {{ $task->priority === 'high' ? 'bg-error' : 'bg-primary' }}"></span>
                            <div class="min-w-0 flex-1"><p class="truncate font-semibold">{{ $task->title }}</p><p class="truncate text-sm text-base-content/50">{{ $task->lead?->organization?->name ?? $task->contact?->full_name ?? 'General task' }}</p></div>
                            <time class="whitespace-nowrap text-xs text-base-content/50">{{ $task->due_at?->timezone('Asia/Colombo')->format('d M') ?? 'No date' }}</time>
                        </div>
                    @empty
                        <div class="p-10 text-center text-base-content/50"><i data-lucide="circle-check-big" class="mx-auto mb-3 size-8 text-success"></i>No open tasks.</div>
                    @endforelse
                </div>
            </div>
        </article>
    </section>

    <section class="grid gap-4 lg:grid-cols-3">
        <article class="card bg-neutral text-neutral-content shadow-sm"><div class="card-body"><div class="flex items-center justify-between"><div><p class="text-sm opacity-60">Prospect organisations</p><p class="mt-2 text-3xl font-black">{{ $organizations }}</p></div><i data-lucide="building-2" class="size-9 opacity-60"></i></div></div></article>
        <article class="card bg-primary text-primary-content shadow-sm"><div class="card-body"><div class="flex items-center justify-between"><div><p class="text-sm opacity-70">Outstanding invoices</p><p class="mt-2 text-3xl font-black">${{ number_format((float)$outstandingInvoices) }}</p></div><i data-lucide="receipt-text" class="size-9 opacity-70"></i></div></div></article>
        <article class="card border border-base-300 bg-base-100 shadow-sm">
            <div class="card-body gap-3">
                <div class="flex items-center justify-between"><div><p class="font-bold">Markets</p><p class="text-xs text-base-content/50">Organisations by country</p></div><i data-lucide="globe-2" class="size-6 text-info"></i></div>
                @forelse($countries as $country)
                    <div class="flex items-center justify-between gap-3 text-sm"><span class="truncate">{{ $country['name'] }}</span><span class="badge badge-ghost badge-sm">{{ $country['total'] }}</span></div>
                @empty
                    <p class="text-sm text-base-content/50">Add a country to an organisation to begin tracking markets.</p>
                @endforelse
            </div>
        </article>
    </section>
</div>
@endsection
