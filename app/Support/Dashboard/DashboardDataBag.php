<?php

namespace App\Support\Dashboard;

use App\Support\Exceptions\DashboardQueryFilterKeyNotFound;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class DashboardDataBag
{
    /** @var Collection */
    protected $data;

    public function __construct()
    {
        $this->data = Collection::make();
    }

    /**
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value): DashboardDataBag
    {
        $this->data->put($key, $value);
        return $this;
    }

    /**
     * @param string|array $keys
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($keys, $defaultValue = null)
    {
        $keys = Arr::wrap($keys);
        $defaultValue = Arr::wrap($defaultValue);

        $filtered = $this->data->only($keys);

        foreach ($keys as $key) {
            if (!$filtered->has($key) && array_key_exists($key, $defaultValue)) {
                $filtered->put($key, $defaultValue[$key]);
            }
        }

        return $filtered->count() > 1 ? $filtered->all() : $filtered->first();
    }

    public function all(): array
    {
        return $this->data->all();
    }
}
