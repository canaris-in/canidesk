<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class DashboardStatisticsConfiguration
{
    /** @var Builder */
    protected $query;

    /** @var Collection */
    protected $queryStatistics;

    public function __construct()
    {
        $this->queryStatistics = Collection::make();
    }

    public function getQuery(): Builder
    {
        return $this->query;
    }

    public function setQuery(Builder $query): DashboardStatisticsConfiguration
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @param mixed $config
     */
    public function addQueryStatistics($config): DashboardStatisticsConfiguration
    {
        $config = Arr::wrap($config);

        $this->queryStatistics = $this->queryStatistics->merge($config);

        return $this;
    }

    public function getQueryStatistics(): array
    {
        return $this->queryStatistics->all();
    }

    public static function make(): DashboardStatisticsConfiguration
    {
        return new static();
    }
}
