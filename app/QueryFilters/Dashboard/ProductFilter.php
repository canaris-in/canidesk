<?php

namespace App\QueryFilters\Dashboard;

use App\Support\Contracts\AbstractQueryFilter;
use App\Support\Dashboard\DashboardFiltersBag;
use App\Support\Dashboard\DashboardQueryManager;
use App\Support\Exceptions\DashboardQueryConfigurationNotFound;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductFilter extends AbstractQueryFilter
{
    public function apply(Builder $query, $filterValue): Builder
    {
        return $query;
//        return $query->where('conversations.type', $filterValue);
    }

    public static function getFilterName(): string
    {
        return 'product';
    }

    public function resolveFilterValue(DashboardFiltersBag $filtersBag): DashboardFiltersBag
    {
        $filtersBag->setFilterValue(static::getFilterName(), $this->request->input('product'));

        return $filtersBag;
    }

    public function preParseFilterValue(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        $dataBag = $dashboardQueryManager->getDataBag();

        $values = DB::table('custom_fields')
            ->where('name', 'Product')
            ->pluck('options')->all();


        $productValues = [];

        if (!empty($values)) {
            $options = json_decode($values[0], true);
            foreach ($options as $key => $value) {
                $productValues[] = $value;
            }

        }

        $dataBag->set('productValues', $productValues);

        $dashboardQueryManager->setDataBag($dataBag);

        return $next($dashboardQueryManager);
    }

    /**
     * @throws DashboardQueryConfigurationNotFound
     */
    public function postParseFilterValue(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        $dataBag = $dashboardQueryManager->getDataBag();
        $filtersBag = $dashboardQueryManager->getFiltersBag();
        $queryConfiguration = $dashboardQueryManager->getQueryConfiguration();

        $filter = $this->getFilterValue($filtersBag, $queryConfiguration);
        $productValues = $dataBag->get('productValues');

        if ($filter === '0' || $filter === null) {
            $productIndex = 0;
        } else {
            $productIndex = array_search($filter, $productValues) + 1;
        }


        $dataBag->set('productIndex', $productIndex);

        $dashboardQueryManager->setDataBag($dataBag);

        return $next($dashboardQueryManager);
    }
}
