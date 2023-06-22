<?php

namespace App\Http\Controllers;

use App\Support\Dashboard\DashboardQueryManager;
use App\QueryConfigurations\{DashboardDefaultQueryConfiguration, TicketsQueryConfiguration};
use App\QueryFilters\Dashboard\{ConversationTypeFilter,
    DateRangeFilter,
    MailboxQueryFilter,
    ProductFilter,
    TicketCategoryFilter};
use App\View\DashboardWidgets\{TicketsCountStatistics, WeeklyTicketsCountStatistics};


class DashboardController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function index()
    {
        $queryConfigurations = [
            'default' => DashboardDefaultQueryConfiguration::class,
            'tickets' => TicketsQueryConfiguration::class,
        ];

        $filtersConfigs = [
            TicketCategoryFilter::class,
            ProductFilter::class,
            MailboxQueryFilter::class,
            ConversationTypeFilter::class,
            DateRangeFilter::class
        ];

        $statisticsConfigs = [
            TicketsCountStatistics::class,
            WeeklyTicketsCountStatistics::class
        ];

        $dashboardQueryManager = DashboardQueryManager::make($queryConfigurations, null, $filtersConfigs, $statisticsConfigs)
            ->callHooksBeforeBuildQueryConfigurations()
            ->buildQueryConfigurations()
            ->callHooksAfterBuildQueryConfigurations()
            ->callHooksBeforeResolveQueryConfigurations()
            ->resolveQueryConfigurations()
            ->callHooksAfterResolveQueryConfigurations();


        /**
         * TODO: (@besrabasant)
         * - Refactor this so that it can access in blade templates directly.
         */
        $filters = $dashboardQueryManager->getFiltersBag()->getValues();

        $dataBag = $dashboardQueryManager->getDataBag();

        // Category Tickets
        return view('dashboard.dashboard',
            compact('filters') + $dataBag->all()
        );
    }
}
