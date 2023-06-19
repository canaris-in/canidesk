<?php

namespace App\View\DashboardWidgets\Contracts;

use App\Support\DashboardStatisticsConfiguration;

abstract class AbstractDashboardStatistics implements DashboardStatisticsContract
{
    /**
     * @return string|array
     */
    abstract public static function getName();

    public function resolveStatistics(DashboardStatisticsConfiguration $dashboardStatisticsConfiguration, $next)
    {
        if ($this instanceof UsesDashboardQuery) {
            $query = $dashboardStatisticsConfiguration->getQuery();
            $statistics = $this->resolve($query);
            $dashboardStatisticsConfiguration = $dashboardStatisticsConfiguration->addQueryStatistics($statistics);
        }


        return $next($dashboardStatisticsConfiguration);
    }
}
