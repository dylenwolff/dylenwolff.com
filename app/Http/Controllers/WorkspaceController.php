<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\CrmTask;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Organization;
use App\Models\OutreachEmail;
use App\Models\OutreachDeliveryMetric;
use App\Support\Countries;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    public function index(): View
    {
        $openLeadStatuses = ['new', 'contacted', 'qualified'];
        $delivery = OutreachDeliveryMetric::current();
        $deliveryRate = $delivery->total_sent > 0
            ? round(($delivery->accepted / $delivery->total_sent) * 100, 1)
            : 0;
        $rejected = max(0, $delivery->bounced - $delivery->invalid_recipient);
        $rejectionRate = $delivery->total_sent > 0
            ? round(($rejected / $delivery->total_sent) * 100, 1)
            : 0;
        $invalidRate = $delivery->total_sent > 0
            ? round(($delivery->invalid_recipient / $delivery->total_sent) * 100, 1)
            : 0;

        return view('workspace.dashboard', [
            'stats' => [
                'openLeads' => Lead::query()->whereIn('status', $openLeadStatuses)->count(),
                'pipelineValue' => Opportunity::query()->whereNotIn('stage', ['won', 'lost'])->sum('amount'),
                'tasksDue' => CrmTask::query()->whereNotIn('status', ['done', 'cancelled'])->where('due_at', '<=', now()->endOfDay())->count(),
                'unreadMessages' => ContactMessage::query()->whereNull('read_at')->where('status', '!=', 'spam')->count(),
                'emailQueue' => OutreachEmail::query()->whereNull('sent_at')->whereIn('status', ['ready', 'approved'])->count(),
                'sentEmails' => OutreachEmail::query()->whereNotNull('sent_at')->count(),
            ],
            'recentLeads' => Lead::query()->with(['organization', 'contact'])->latest()->limit(7)->get(),
            'dueTasks' => CrmTask::query()->with(['lead.organization', 'contact'])
                ->whereNotIn('status', ['done', 'cancelled'])
                ->orderByRaw("case priority when 'urgent' then 1 when 'high' then 2 when 'normal' then 3 else 4 end")
                ->orderByRaw('due_at IS NULL')->orderBy('due_at')->limit(6)->get(),
            'organizations' => Organization::query()->count(),
            'countries' => Organization::query()
                ->whereNotNull('country_code')
                ->selectRaw('country_code, count(*) as total')
                ->groupBy('country_code')
                ->orderByDesc('total')
                ->limit(6)
                ->get()
                ->map(fn ($country) => [
                    'code' => $country->country_code,
                    'name' => Countries::name($country->country_code),
                    'total' => $country->total,
                ]),
            'outstandingInvoices' => Invoice::query()->whereNotIn('status', ['paid', 'cancelled'])->sum('total'),
            'delivery' => [
                'total' => $delivery->total_sent,
                'accepted' => $delivery->accepted,
                'failed' => $delivery->bounced,
                'rejected' => $rejected,
                'delayed' => $delivery->delayed,
                'reputation' => $delivery->reputation_blocked,
                'invalid' => $delivery->invalid_recipient,
                'server' => $delivery->server_rejected,
                'deliveryRate' => $deliveryRate,
                'rejectionRate' => $rejectionRate,
                'invalidRate' => $invalidRate,
                'measuredAt' => $delivery->measured_at,
            ],
        ]);
    }
}
