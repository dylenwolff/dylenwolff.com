<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\CrmTask;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Organization;
use App\Support\Countries;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    public function index(): View
    {
        $openLeadStatuses = ['new', 'contacted', 'qualified'];

        return view('workspace.dashboard', [
            'stats' => [
                'openLeads' => Lead::query()->whereIn('status', $openLeadStatuses)->count(),
                'pipelineValue' => Opportunity::query()->whereNotIn('stage', ['won', 'lost'])->sum('amount'),
                'tasksDue' => CrmTask::query()->whereNotIn('status', ['done', 'cancelled'])->where('due_at', '<=', now()->endOfDay())->count(),
                'unreadMessages' => ContactMessage::query()->whereNull('read_at')->count(),
            ],
            'recentLeads' => Lead::query()->with(['organization', 'contact'])->latest()->limit(7)->get(),
            'dueTasks' => CrmTask::query()->with(['lead.organization', 'contact'])->whereNotIn('status', ['done', 'cancelled'])->orderByRaw('due_at IS NULL')->orderBy('due_at')->limit(6)->get(),
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
        ]);
    }
}
