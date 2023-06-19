<?php

namespace App\Support;

use App\Support\Exceptions\DashboardQueryFilterKeyNotFound;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Traversable;

class DashboardQueryConfiguration
{
    /**
     * @var Builder
     */
    protected $query;

    /**
     * @var Collection
     */
    protected $filters;

    public function __construct()
    {
        $this->filters = Collection::make();
    }

    /**
     * @param Builder $query
     * @return $this
     */
    public function setQuery(Builder $query): DashboardQueryConfiguration
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
    public function setFilter($filter): DashboardQueryConfiguration
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

    public static function make(): DashboardQueryConfiguration
    {
        return new static();
    }
}
