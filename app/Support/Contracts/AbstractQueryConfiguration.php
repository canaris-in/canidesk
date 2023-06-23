<?php

namespace App\Support\Contracts;

use App\Support\Dashboard\DashboardDataBag;
use App\Support\Dashboard\DashboardFiltersBag;
use App\Support\Dashboard\DashboardQueryManager;
use App\Support\Dashboard\DashboardStatisticsConfiguration;
use App\Support\Exceptions\DashboardConfigurationNotResolved;
use App\Support\Exceptions\DashboardQueryFilterKeyNotFound;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Traversable;

abstract class AbstractQueryConfiguration
{
    /**
     * @var Builder|\Illuminate\Database\Eloquent\Collection
     */
    protected $query;

    /**
     * @var Collection
     */
    protected $filters;

    /**
     * @var mixed
     */
    protected $queryResults = null;

    public function __construct()
    {
        $this->filters = Collection::make();
    }

    /**
     * @param DashboardDataBag $dashboardDataBag
     * @param DashboardFiltersBag $dashboardFiltersBag
     * @param DashboardQueryManager $dashboardQueryManager
     * @return Builder
     */
    abstract public function buildQuery(
        DashboardDataBag      $dashboardDataBag,
        DashboardFiltersBag   $dashboardFiltersBag,
        DashboardQueryManager $dashboardQueryManager
    ): Builder;

    abstract public function resolveQuery(
        DashboardStatisticsConfiguration $statisticsConfiguration,
        DashboardDataBag                 $dashboardDataBag,
        DashboardQueryManager            $dashboardQueryManager
    );


    /**
     * @param Builder $query
     * @return $this
     */
    public function setQuery(Builder $query): AbstractQueryConfiguration
    {
        $this->query = $query;
        return $this;
    }

    public function getQuery(): Builder
    {
        return $this->query;
    }

    /**
     * @param array|Traversable $filter
     * @return $this
     */
    public function setFilter($filter): AbstractQueryConfiguration
    {
        $this->filters = $this->filters->merge($filter);
        return $this;
    }

    /**
     * @param string|array|null $keys
     * @throws \Throwable
     * @returns array|string|mixed
     */
    public function getFilter($keys = null)
    {
        if (!is_null($keys)) {

            $keys = Arr::wrap($keys);

            throw_if(!$this->filters->has($keys), new DashboardQueryFilterKeyNotFound());

            $filtered = $this->filters->only($keys);

            return $filtered->count() > 1 ? $filtered->all() : $filtered->first();
        }

        return $this->filters->all();
    }

    public static function make(): AbstractQueryConfiguration
    {
        return new static();
    }

    /**
     * @throws \Throwable
     */
    public function getResults()
    {
        throw_if(is_null($this->queryResults), new DashboardConfigurationNotResolved($this));

        return $this->queryResults;
    }

    public function setResults($results)
    {
        return $this->queryResults = $results;
    }
}
