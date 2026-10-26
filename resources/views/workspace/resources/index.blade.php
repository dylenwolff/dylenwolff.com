@extends('layouts.workspace')

@section('title', $resource['label'])
@section('heading', $resource['label'])

@section('content')
<div class="mx-auto max-w-[1600px] space-y-5">
    @if(session('success'))<div class="alert alert-success"><i data-lucide="circle-check" class="size-5"></i><span>{{ session('success') }}</span></div>@endif
    @if(session('warning'))<div class="alert alert-warning"><i data-lucide="triangle-alert" class="size-5"></i><span>{{ session('warning') }}</span></div>@endif
    @if(session('error'))<div class="alert alert-error"><i data-lucide="circle-alert" class="size-5"></i><span>{{ session('error') }}</span></div>@endif
    <section class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div><h2 class="text-2xl font-black tracking-tight">{{ $resource['label'] }}</h2><p class="mt-1 text-sm text-base-content/55">Manage {{ strtolower($resource['label']) }} from your workspace.</p></div>
        @if(($resource['create'] ?? true) !== false)<a href="{{ route('workspace.resources.create', $section) }}" class="btn btn-primary"><i data-lucide="plus" class="size-4"></i>Add {{ strtolower($resource['singular']) }}</a>@endif
    </section>

    <form class="card border border-base-300 bg-base-100 shadow-sm" method="GET">
        <div class="card-body flex-row flex-wrap items-end gap-3 p-4">
            <label class="form-control min-w-64 flex-1"><span class="label-text mb-1 text-xs font-bold">Search</span><input name="search" value="{{ request('search') }}" class="input input-bordered w-full" placeholder="Search {{ strtolower($resource['label']) }}"></label>
            @if($countries)<label class="form-control w-full md:w-64"><span class="label-text mb-1 text-xs font-bold">Country</span><select name="country" class="select select-bordered"><option value="">All countries</option>@foreach($countries as $code => $name)<option value="{{ $code }}" @selected(request('country') === $code)>{{ $name }}</option>@endforeach</select></label>@endif
            @if($statuses)<label class="form-control w-full md:w-56"><span class="label-text mb-1 text-xs font-bold">Status</span><select name="status" class="select select-bordered"><option value="">{{ $statusDefaultLabel }}</option>@if($section === 'contact-messages')<option value="all" @selected(request('status') === 'all')>All messages</option>@endif @foreach($statuses as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></label>@endif
            <button class="btn btn-outline"><i data-lucide="search" class="size-4"></i>Filter</button>
            @if(request()->hasAny(['search','country','status']))<a href="{{ route('workspace.manage', $section) }}" class="btn btn-ghost">Clear</a>@endif
        </div>
    </form>

    <section class="card border border-base-300 bg-base-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead><tr>@foreach($resource['columns'] as $column)<th>{{ \App\Support\WorkspaceResources::label(last(explode('.', $column))) }}</th>@endforeach<th class="text-right">Actions</th></tr></thead>
                <tbody>
                @forelse($records as $record)
                    <tr>
                    @foreach($resource['columns'] as $column)
                        @php($value = data_get($record, $column))
                        <td class="max-w-72">
                            @if(str_starts_with(last(explode('.', $column)), 'is_'))
                                <span class="badge {{ $value ? 'badge-success' : 'badge-ghost' }} badge-sm">{{ $value ? 'Yes' : 'No' }}</span>
                            @elseif(in_array(last(explode('.', $column)), ['status','stage','priority','source','type','billing_type'], true))
                                <span class="badge badge-outline badge-sm">{{ str((string)$value)->replace('_', ' ')->headline() }}</span>
                            @elseif(last(explode('.', $column)) === 'phone' && filled($value))
                                @php($internationalPhone = \App\Support\PhoneNumber::international($value, $record->country_code ?? $record->organization?->country_code))
                                <a class="link link-primary whitespace-nowrap" href="tel:{{ $internationalPhone }}">{{ $internationalPhone }}</a>
                            @elseif(last(explode('.', $column)) === 'contact_phone' && filled($value))
                                <a class="link link-primary whitespace-nowrap" href="tel:{{ preg_replace('/[^+\d]/', '', $value) }}">{{ $value }}</a>
                            @elseif(last(explode('.', $column)) === 'country_code')
                                {{ \App\Support\Countries::name($value) ?? 'Not set' }}
                            @elseif($value instanceof \Carbon\CarbonInterface)
                                <span class="whitespace-nowrap">{{ $value->timezone('Asia/Colombo')->format($value->format('H:i:s') === '00:00:00' ? 'd M Y' : 'd M Y, H:i') }}</span>
                            @elseif(is_bool($value))
                                {{ $value ? 'Yes' : 'No' }}
                            @elseif(in_array(last(explode('.', $column)), ['amount','fee','total','monthly_fee','estimated_value','amount_paid'], true) && filled($value))
                                <span class="whitespace-nowrap font-mono">{{ $record->currency ?? 'LKR' }} {{ number_format((float)$value, 2) }}</span>
                            @else
                                <span class="line-clamp-2">{{ filled($value) ? $value : 'Not set' }}</span>
                            @endif
                        </td>
                    @endforeach
                        <td><div class="flex justify-end gap-1">
                            @if($section === 'leads' && \App\Support\WhatsAppLink::url($record))
                                <form method="POST" action="{{ route('workspace.leads.whatsapp', $record) }}" target="_blank" onsubmit="return confirm('Open this tailored message in WhatsApp? The CRM will record that the draft was opened, not that it was sent.')">@csrf<button class="btn btn-square btn-success btn-sm" aria-label="Open WhatsApp message" title="Open WhatsApp message"><i data-lucide="message-circle" class="size-4"></i></button></form>
                            @endif
                            @if($section === 'outreach-emails' && in_array($record->status, ['ready', 'approved'], true) && !$record->sent_at)
                                <form method="POST" action="{{ route('workspace.outreach-emails.send', $record) }}" onsubmit="return confirm('Send this email now?')">@csrf<button class="btn btn-square btn-success btn-sm" aria-label="Send email"><i data-lucide="send" class="size-4"></i></button></form>
                            @endif
                            <a href="{{ route('workspace.resources.show', [$section, $record]) }}" class="btn btn-square btn-ghost btn-sm" aria-label="View" title="View"><i data-lucide="eye" class="size-4"></i></a>
                            <a href="{{ route('workspace.resources.edit', [$section, $record]) }}" class="btn btn-square btn-ghost btn-sm" aria-label="Edit"><i data-lucide="pencil" class="size-4"></i></a><form method="POST" action="{{ route('workspace.resources.destroy', [$section, $record]) }}" onsubmit="return confirm('Delete this {{ strtolower($resource['singular']) }}?')">@csrf @method('DELETE')<button class="btn btn-square btn-ghost btn-sm text-error" aria-label="Delete"><i data-lucide="trash-2" class="size-4"></i></button></form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($resource['columns']) + 1 }}" class="py-16 text-center text-base-content/50"><i data-lucide="inbox" class="mx-auto mb-3 size-9"></i>No records found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())<div class="border-t border-base-300 p-4">{{ $records->links() }}</div>@endif
    </section>
</div>
@endsection
