@extends('layouts.workspace')

@section('title', 'View '.$resource['singular'])
@section('heading', $resource['label'])

@section('content')
@php
    $title = data_get($model, $resource['title']) ?: $resource['singular'].' #'.$model->getKey();
    $state = $model->status ?? $model->stage ?? $model->priority ?? null;
    $wideFields = ['body', 'description', 'notes', 'details', 'message', 'summary', 'challenge', 'solution', 'responsibilities', 'scope', 'services'];
@endphp
<div class="mx-auto max-w-6xl space-y-5">
    <section class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div class="flex min-w-0 items-start gap-3">
            <a href="{{ route('workspace.manage', $section) }}" class="btn btn-square btn-ghost" aria-label="Back"><i data-lucide="arrow-left" class="size-5"></i></a>
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="truncate text-2xl font-black">{{ $title }}</h2>
                    @if($state)<span class="badge badge-outline">{{ str((string) $state)->replace('_', ' ')->headline() }}</span>@endif
                </div>
                <p class="mt-1 text-sm text-base-content/55">{{ $resource['singular'] }} record created {{ $model->created_at?->timezone('Asia/Colombo')->format('d M Y, H:i') ?? 'previously' }}</p>
            </div>
        </div>
        <a href="{{ route('workspace.resources.edit', [$section, $model]) }}" class="btn btn-primary"><i data-lucide="pencil" class="size-4"></i>Edit {{ strtolower($resource['singular']) }}</a>
    </section>

    @if($section === 'client-projects')
        <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([['Invoices', $model->invoices_count, 'receipt-text'], ['Documents', $model->documents_count, 'folder-lock'], ['Support tickets', $model->support_tickets_count, 'life-buoy'], ['Maintenance plans', $model->maintenance_plans_count, 'refresh-cw']] as [$label, $count, $icon])
                <div class="card border border-base-300 bg-base-100 shadow-sm"><div class="card-body flex-row items-center gap-3 p-4"><span class="grid size-10 place-items-center rounded-box bg-primary/10 text-primary"><i data-lucide="{{ $icon }}" class="size-5"></i></span><div><p class="text-xs font-bold uppercase tracking-wide text-base-content/50">{{ $label }}</p><p class="text-2xl font-black">{{ $count }}</p></div></div></div>
            @endforeach
        </section>
    @endif

    @if($section === 'crm-tasks')
        <section class="grid gap-3 md:grid-cols-3">
            <div class="card border border-base-300 bg-base-100 shadow-sm"><div class="card-body p-4"><p class="text-xs font-bold uppercase tracking-wide text-base-content/50">Organization</p><p class="mt-1 font-bold">{{ $model->organization_name ?: 'Not linked' }}</p></div></div>
            <div class="card border border-base-300 bg-base-100 shadow-sm"><div class="card-body p-4"><p class="text-xs font-bold uppercase tracking-wide text-base-content/50">Contact</p><p class="mt-1 font-bold">{{ $model->contact_name ?: 'Owner or manager' }}</p></div></div>
            <div class="card border border-base-300 bg-base-100 shadow-sm"><div class="card-body p-4"><p class="text-xs font-bold uppercase tracking-wide text-base-content/50">Phone</p>@if($model->contact_phone)<a class="link link-primary mt-1 font-bold" href="tel:{{ preg_replace('/[^+\d]/', '', $model->contact_phone) }}">{{ $model->contact_phone }}</a>@else<p class="mt-1 text-base-content/45">Not available</p>@endif</div></div>
        </section>
    @endif

    <section class="card border border-base-300 bg-base-100 shadow-sm">
        <div class="card-body p-0">
            <div class="grid md:grid-cols-2">
                @foreach($fields as $field)
                    @php
                        $value = data_get($model, $field['name']);
                        $display = $field['options'][$value] ?? $value;
                        $isWide = in_array($field['name'], $wideFields, true) || is_array($value);
                    @endphp
                    <div class="border-b border-base-300 p-5 {{ $isWide ? 'md:col-span-2' : 'md:odd:border-r' }}">
                        <p class="mb-2 text-xs font-bold uppercase tracking-[.12em] text-base-content/45">{{ $field['label'] }}</p>
                        @if($value instanceof \Carbon\CarbonInterface)
                            <p>{{ $value->timezone('Asia/Colombo')->format($value->format('H:i:s') === '00:00:00' ? 'd M Y' : 'd M Y, H:i') }}</p>
                        @elseif(is_bool($value))
                            <span class="badge {{ $value ? 'badge-success' : 'badge-ghost' }}">{{ $value ? 'Yes' : 'No' }}</span>
                        @elseif(is_array($value))
                            <div class="flex flex-wrap gap-2">@forelse($value as $item)<span class="badge badge-outline">{{ $item }}</span>@empty<span class="text-base-content/40">Not set</span>@endforelse</div>
                        @elseif($field['name'] === 'email' || str_ends_with($field['name'], '_email'))
                            @if($value)<a class="link link-primary break-all" href="mailto:{{ $value }}">{{ $value }}</a>@else<span class="text-base-content/40">Not set</span>@endif
                        @elseif(in_array($field['name'], ['website', 'url'], true))
                            @if($value)<a class="link link-primary break-all" href="{{ $value }}" target="_blank" rel="noopener">{{ $value }}</a>@else<span class="text-base-content/40">Not set</span>@endif
                        @elseif($field['name'] === 'phone')
                            @if($value)<a class="link link-primary" href="tel:{{ preg_replace('/[^+\d]/', '', $value) }}">{{ $value }}</a>@else<span class="text-base-content/40">Not set</span>@endif
                        @elseif(in_array($field['name'], ['status', 'stage', 'priority', 'source', 'type', 'billing_type'], true) && filled($display))
                            <span class="badge badge-outline">{{ str((string) $display)->replace('_', ' ')->headline() }}</span>
                        @elseif(in_array($field['name'], ['amount', 'fee', 'total', 'monthly_fee', 'estimated_value', 'amount_paid'], true) && filled($value))
                            <p class="font-mono text-lg font-bold">{{ $model->currency ?? 'LKR' }} {{ number_format((float) $value, 2) }}</p>
                        @elseif(in_array($field['name'], $wideFields, true))
                            <div class="whitespace-pre-wrap leading-7">{{ filled($display) ? $display : 'Not set' }}</div>
                        @else
                            <p class="break-words">{{ filled($display) ? $display : 'Not set' }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 p-5 text-sm text-base-content/50">
                <span>Record #{{ $model->getKey() }}</span>
                <span>Last updated {{ $model->updated_at?->timezone('Asia/Colombo')->format('d M Y, H:i') }}</span>
            </div>
        </div>
    </section>
</div>
@endsection
