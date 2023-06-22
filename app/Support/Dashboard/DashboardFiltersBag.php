<?php

namespace App\Support\Dashboard;

use App\Support\Exceptions\DashboardQueryFilterKeyNotFound;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class DashboardFiltersBag
{
    /** @var Collection */
    protected $filters;

    public function __construct()
    {
        $this->filters = Collection::make();
    }

    /**
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setFilterValue($key, $value): DashboardFiltersBag
    {
        $this->filters->put($key, $value);
        return $this;
    }

    /**
     * @param string|array $keys
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getFilterValue($keys, $defaultValue = null)
    {
        $keys = Arr::wrap($keys);

        $filtered = $this->filters->only($keys);

        foreach ($keys as $key) {
            if (!$filtered->has($key)) {
                $filtered->put($key, $defaultValue[$key]);
            }
        }

        return $filtered->count() > 1 ? $filtered->all() : $filtered->first();
    }

    public function getValues(): array
    {
        return $this->filters->all();
    }
}
