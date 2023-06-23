<?php

namespace App\View\DashboardWidgets;

use App\Support\Contracts\AbstractQueryConfiguration;
use App\Support\Dashboard\DashboardDataBag;
use App\Support\Dashboard\DashboardQueryManager;
use App\View\DashboardWidgets\Contracts\{AbstractDashboardStatistics, UsesQueryConfiguration};
use Illuminate\Database\Eloquent\Builder;

class WeeklyTicketsCountStatistics extends AbstractDashboardStatistics implements UsesQueryConfiguration
{

    public function build(Builder $query): Builder
    {
        return $query;
    }

    public function useQueryConfiguration(): string
    {
        return 'tickets';
    }

    /**
     * @param AbstractQueryConfiguration $queryConfiguration
     * @param DashboardDataBag $dashboardDataBag
     * @param DashboardQueryManager $dashboardQueryManager
     * @throws \Throwable
     */
    public function resolve(
        AbstractQueryConfiguration $queryConfiguration,
        DashboardDataBag                   $dashboardDataBag,
        DashboardQueryManager              $dashboardQueryManager)
    {
        // Extract the data
        $ticketsInitial = $queryConfiguration->getResults();

        $daysOfWeek = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
        ];

        $tickets = [];
        foreach ($daysOfWeek as $day) {
            $tickets[$day] = $ticketsInitial[$day] ?? 0;
        };

        $dashboardDataBag->set('tickets', $tickets);
    }
}
