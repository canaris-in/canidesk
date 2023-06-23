<?php

namespace App\Support\Contracts;

use App\Support\Dashboard\DashboardFiltersBag;
use App\Support\Dashboard\DashboardQueryManager;
use Illuminate\Http\Request;

abstract class AbstractQueryFilter implements QueryFilterStageContract
{
    /** @var Request */
    protected $request;

    protected $useQueryConfiguration = 'default';

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function useQueryConfiguration(): string
    {
        return $this->useQueryConfiguration;
    }

    /**
     * @return string|array
     */
    abstract public static function getFilterName();

    /**
     * @throws \Throwable
     */
    public function parseFilter(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {


        $queryConfiguration = $dashboardQueryManager->getQueryConfiguration($this->useQueryConfiguration());
        $filterBag = $dashboardQueryManager->getFiltersBag();

        $query = $queryConfiguration->getQuery();

        $filter = $this->getFilterValue($filterBag, $queryConfiguration);

        if (!empty($filter)) {
            $query = $this->apply($query, $filter);
        }

        $queryConfiguration->setQuery($query);

        $dashboardQueryManager->setQueryConfiguration($this->useQueryConfiguration(), $queryConfiguration);

        return $next($dashboardQueryManager);
    }

    protected function getFilterValue($filterBag, $queryConfiguration)
    {
        try {
            $filter = $filterBag->getFilterValue(static::getFilterName());
        } catch (\Exception $exception) {
            $filter = $queryConfiguration->getFilter(static::getFilterName());
        }

        return $filter;
    }

    public function resolveFilterValue(DashboardFiltersBag $filtersBag): DashboardFiltersBag
    {
        return $filtersBag;
    }

    public function preParseFilterValue(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        return $next($dashboardQueryManager);
    }

    public function postParseFilterValue(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        return $next($dashboardQueryManager);
    }

    public function parseFilterValue(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        return $next(
            $dashboardQueryManager->setFiltersBag(
                $this->resolveFilterValue(
                    $dashboardQueryManager->getFiltersBag()
                )
            )
        );
    }

    public function preBuildQueryConfigurations(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        return $next($dashboardQueryManager);
    }

    public function postBuildQueryConfigurations(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        return $next($dashboardQueryManager);
    }

    public function postParseFilter(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        return $next($dashboardQueryManager);
    }

}
