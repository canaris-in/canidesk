<?php

namespace App\QueryFilters\Dashboard;

use App\Support\DashboardQueryConfiguration;
use App\Support\QueryFilterStageContract;

abstract class AbstractQueryFilter implements QueryFilterStageContract
{

    /**
     * @return string|array
     */
    abstract public static function getFilterName();

    /**
     * @throws \Throwable
     */
    public function parseFilter(DashboardQueryConfiguration $queryConfiguration, \Closure $next) {
        $query = $queryConfiguration->getQuery();
        $filter = $queryConfiguration->getFilter(static::getFilterName());

        if (!empty($filter)) {
            $query = $this->apply($query, $filter);
        }

        $queryConfiguration->setQuery($query);
        return $next($queryConfiguration);
    }
}
