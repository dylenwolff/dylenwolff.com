<?php

namespace App\Filament\Widgets;

use App\Models\CrmTask;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\MaintenancePlan;
use App\Models\Opportunity;
use App\Models\SupportTicket;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $openPipeline = Opportunity::query()->whereNotIn('stage', ['won', 'lost'])->sum('amount');
        $recurringRevenue = MaintenancePlan::query()->where('status', 'active')->sum('monthly_fee');
        $outstandingInvoices = Invoice::query()->whereNotIn('status', ['paid', 'void'])->get()->sum(
            fn (Invoice $invoice): float => max(0, (float) $invoice->total - (float) $invoice->amount_paid)
        );

        return [
            Stat::make('New leads', Lead::query()->where('status', 'new')->count())
                ->description('Waiting for first contact')
                ->color('info'),
            Stat::make('Open pipeline', 'LKR '.number_format((float) $openPipeline, 2))
                ->description('Potential sales value')
                ->color('success'),
            Stat::make('Tasks due', CrmTask::query()->whereNotIn('status', ['done', 'cancelled'])->where('due_at', '<=', now()->endOfDay())->count())
                ->description('Due today or overdue')
                ->color('warning'),
            Stat::make('Monthly recurring', 'LKR '.number_format((float) $recurringRevenue, 2))
                ->description('Active maintenance plans')
                ->color('success'),
            Stat::make('Outstanding invoices', 'LKR '.number_format($outstandingInvoices, 2))
                ->description('Still to be collected')
                ->color('danger'),
            Stat::make('Open support', SupportTicket::query()->whereNotIn('status', ['resolved', 'closed'])->count())
                ->description('Tickets needing attention')
                ->color('warning'),
        ];
    }
}
