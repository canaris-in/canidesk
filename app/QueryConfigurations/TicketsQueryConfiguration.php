<?php

namespace App\QueryConfigurations;

use App\Conversation;
use App\Support\Contracts\AbstractQueryConfiguration;
use App\Support\Dashboard\DashboardDataBag;
use App\Support\Dashboard\DashboardFiltersBag;
use App\Support\Dashboard\DashboardQueryManager;
use App\Support\Dashboard\DashboardStatisticsConfiguration;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class TicketsQueryConfiguration extends AbstractQueryConfiguration
{
    public function buildQuery(
        DashboardDataBag      $dashboardDataBag,
        DashboardFiltersBag   $dashboardFiltersBag,
        DashboardQueryManager $dashboardQueryManager
    ): Builder
    {
        $from = Carbon::parse($dashboardFiltersBag->getFilterValue('from'));

        $to = Carbon::parse($dashboardFiltersBag->getFilterValue('to'));

        $startDate = $from->startOfWeek();

        $endDate = $to->endOfWeek();

//        $startDate = now()->startOfWeek();
//        $endDate = now()->endOfWeek();

        return Conversation::selectRaw('DAYNAME(created_at) as day, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('day');
    }

    public function resolveQuery(DashboardStatisticsConfiguration $statisticsConfiguration, DashboardDataBag $dashboardDataBag, DashboardQueryManager $dashboardQueryManager)
    {
        return $this->query->pluck('count', 'day')->toArray();
    }
}
