<?php

namespace App\View\DashboardWidgets\Contracts;

use App\Support\Dashboard\DashboardQueryManager;
use App\Support\Exceptions\DashboardQueryConfigurationNotFound;
use App\Support\Exceptions\DashboardStatisticsConfigurationNotFound;
use Illuminate\Database\Eloquent\Builder;

abstract class AbstractDashboardStatistics implements DashboardStatisticsContract
{

    /**
     * @throws DashboardQueryConfigurationNotFound
     * @throws DashboardStatisticsConfigurationNotFound
     */
    public function buildStatistics(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        if ($this instanceof UsesQueryConfiguration) {
            $queryConfiguration = $dashboardQueryManager->getQueryConfiguration($this->useQueryConfiguration());
            $statisticsConfiguration = $dashboardQueryManager->getStatisticsConfiguration();
            $query = $queryConfiguration->getQuery();
            $statistics = $this->build($query);
            if ($statistics instanceof Builder) {
                $queryConfiguration->setQuery($query);
            } else {
                $statisticsConfiguration = $statisticsConfiguration->addQueryStatistics($statistics);
            }
            $dashboardQueryManager->setStatisticsConfiguration($this->useQueryConfiguration(), $statisticsConfiguration);
        }

        return $next($dashboardQueryManager);
    }

    /**
     * @throws \Throwable
     */
    public function resolveStatistics(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        if ($this instanceof UsesQueryConfiguration) {
            $this->resolve(
                $dashboardQueryManager->getQueryConfiguration($this->useQueryConfiguration()),
                $dashboardQueryManager->getDataBag(),
                $dashboardQueryManager
            );
        }

        return $next($dashboardQueryManager);
    }

    /**
     * @throws \Throwable
     */
    public function preParseFilterValue(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        return $next($dashboardQueryManager);
    }

    /**
     * @throws \Throwable
     */
    public function preBuildQueryConfigurations(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        return $next($dashboardQueryManager);
    }

    /**
     * @throws \Throwable
     */
    public function postBuildQueryConfigurations(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        return $next($dashboardQueryManager);
    }

    /**
     * @throws \Throwable
     */
    public function preBuildStatistics(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        return $next($dashboardQueryManager);
    }

    /**
     * @throws \Throwable
     */
    public function preResolveStatistics(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        return $next($dashboardQueryManager);
    }
}
