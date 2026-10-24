<?php

namespace App\Filament\Widgets;

use App\Models\CrmTask;
use App\Models\Lead;
use App\Models\Opportunity;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $openPipeline = Opportunity::query()->whereNotIn('stage', ['won', 'lost'])->sum('amount');

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
        ];
    }
}
