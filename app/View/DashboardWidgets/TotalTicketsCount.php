<?php

namespace App\View\DashboardWidgets;

use App\View\DashboardWidgets\Contracts\{AbstractDashboardStatistics, UsesDashboardQuery};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TotalTicketsCount extends AbstractDashboardStatistics implements UsesDashboardQuery
{
    public static function getName(): string
    {
        return 'total_count';
    }

    public function resolve(Builder $query): array
    {
        return [
            DB::raw('COUNT(*) as total_count'),
            DB::raw('COUNT(CASE WHEN created_by_user_id IS NULL THEN 1 END) as unassigned_count'),
            // DB::raw('COUNT(CASE WHEN closed_at IS NULL THEN 1 END) as overdue_count'),
            // DB::raw('COUNT(CASE WHEN created_at < ? AND closed_at IS NULL THEN 1 END) as overdue_count'),
            DB::raw('COUNT(CASE WHEN closed_at IS NULL THEN 1 END) as unclosed_count'),
            DB::raw('COUNT(CASE WHEN closed_at IS NOT NULL THEN 1 END) as closed_tickets_count'),
            DB::raw('COUNT(CASE WHEN closed_at IS NULL THEN 1 END) as unclosed_created_30_days_ago_count')
        ];
    }
}
