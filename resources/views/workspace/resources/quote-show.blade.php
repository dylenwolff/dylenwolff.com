@push('styles')
<style>
    @media print {
        @page { size: A4; margin: 13mm; }
        html, body { background: #fff !important; color: #111827 !important; }
        body > .drawer > .drawer-content > header,
        .drawer-side,
        .quote-print-actions { display: none !important; }
        .drawer-content { margin: 0 !important; }
        .drawer-content > main { padding: 0 !important; }
        .quote-document { max-width: none !important; border: 0 !important; box-shadow: none !important; }
        .quote-document .card-body { padding: 0 !important; }
        .quote-document * { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
    }
</style>
@endpush

@php
    $organization = $model->organization;
    $issuedAt = $model->issued_at?->timezone('Asia/Colombo');
    $validUntil = $model->valid_until?->timezone('Asia/Colombo');
    $isMonthlyService = str_contains(strtolower((string) $model->scope), 'monthly managed service');
    $includesFirstMonthDomain = str_contains(strtolower((string) $model->scope), 'first month:');
@endphp

<div class="mx-auto max-w-5xl space-y-5">
    <div class="quote-print-actions flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('workspace.manage', 'quotes') }}" class="btn btn-ghost">
            <i data-lucide="arrow-left" class="size-4"></i>Back to quotes
        </a>
        <div class="flex gap-2">
            <a href="{{ route('workspace.resources.edit', ['quotes', $model]) }}" class="btn btn-outline">
                <i data-lucide="pencil" class="size-4"></i>Edit
            </a>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i data-lucide="printer" class="size-4"></i>Print or save PDF
            </button>
        </div>
    </div>

    <article class="quote-document card border border-base-300 bg-base-100 shadow-sm">
        <div class="card-body gap-8 p-7 sm:p-10">
            <header class="flex flex-col justify-between gap-6 border-b border-base-300 pb-7 sm:flex-row sm:items-start">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/brand/dylen-wolff-mark.svg') }}" alt="Dylen Wolff" class="size-14 rounded-2xl bg-primary p-2">
                    <div>
                        <h2 class="text-xl font-black">Dylen Wolff</h2>
                        <p class="text-sm text-base-content/60">Software Engineer &amp; Digital Solutions Developer</p>
                        <a href="mailto:connect@dylenwolff.com" class="link link-primary text-sm">connect@dylenwolff.com</a>
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-xs font-bold uppercase tracking-[.2em] text-primary">Quotation</p>
                    <h1 class="mt-1 text-3xl font-black">{{ $model->number }}</h1>
                    <span class="badge badge-outline mt-2">{{ str($model->status)->headline() }}</span>
                </div>
            </header>

            <section class="grid gap-6 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.16em] text-base-content/45">Prepared for</p>
                    <h3 class="mt-2 text-lg font-black">{{ $organization?->name ?? 'Client' }}</h3>
                    @if($organization?->city || $organization?->country_code)
                        <p class="text-sm text-base-content/65">{{ collect([$organization?->city, \App\Support\Countries::name($organization?->country_code)])->filter()->join(', ') }}</p>
                    @endif
                    @if($organization?->website)
                        <p class="mt-1 text-sm">{{ $organization->website }}</p>
                    @endif
                </div>
                <dl class="grid grid-cols-2 gap-x-5 gap-y-3 text-sm sm:justify-self-end">
                    <dt class="text-base-content/50">Issued</dt><dd class="font-semibold">{{ $issuedAt?->format('d M Y') ?? 'Not set' }}</dd>
                    <dt class="text-base-content/50">Valid until</dt><dd class="font-semibold">{{ $validUntil?->format('d M Y') ?? 'Not set' }}</dd>
                    <dt class="text-base-content/50">Currency</dt><dd class="font-semibold">{{ $model->currency }}</dd>
                </dl>
            </section>

            <section>
                <h3 class="mb-3 text-xs font-bold uppercase tracking-[.16em] text-base-content/45">Project scope</h3>
                <div class="whitespace-pre-line leading-7 text-base-content/85">{{ $model->scope }}</div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-base-300">
                <div class="flex items-center justify-between bg-base-200 px-5 py-4">
                    <span class="font-bold">{{ $includesFirstMonthDomain ? 'First month including domain' : ($isMonthlyService ? 'Monthly managed service' : 'Quoted investment') }}</span>
                    <span class="text-2xl font-black text-primary">{{ $model->currency }} {{ number_format((float) $model->total, 2) }}</span>
                </div>
                @if((float) $model->tax > 0)
                    <div class="flex justify-between px-5 py-3 text-sm"><span>Tax</span><span>{{ $model->currency }} {{ number_format((float) $model->tax, 2) }}</span></div>
                @endif
            </section>

            <section>
                <h3 class="mb-3 text-xs font-bold uppercase tracking-[.16em] text-base-content/45">Terms and notes</h3>
                <div class="whitespace-pre-line leading-7 text-base-content/80">{{ $model->notes }}</div>
            </section>

            <footer class="border-t border-base-300 pt-5 text-xs text-base-content/50">
                <p>This quotation is valid until {{ $validUntil?->format('d F Y') ?? 'the date stated above' }}.</p>
                <p class="mt-1">dylenwolff.com · connect@dylenwolff.com · Colombo, Sri Lanka</p>
            </footer>
        </div>
    </article>
</div>
