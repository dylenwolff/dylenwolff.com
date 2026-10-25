<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\CrmTask;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Organization;
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
            'outstandingInvoices' => Invoice::query()->whereNotIn('status', ['paid', 'cancelled'])->sum('total'),
        ]);
    }
}
