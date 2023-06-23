<?php

namespace App\QueryFilters\Dashboard;

use App\Support\Contracts\AbstractQueryFilter;
use App\Support\Dashboard\DashboardFiltersBag;
use App\Support\Dashboard\DashboardQueryManager;
use App\Support\Exceptions\DashboardQueryConfigurationNotFound;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TicketCategoryFilter extends AbstractQueryFilter
{
    public function apply(Builder $query, $filterValue): Builder
    {
        return $query;
//        return $query->where('conversations.type', $filterValue);
    }

    public static function getFilterName(): string
    {
        return 'ticket';
    }

    public function resolveFilterValue(DashboardFiltersBag $filtersBag): DashboardFiltersBag
    {
        $filtersBag->setFilterValue(static::getFilterName(), $this->request->input('ticket'));

        return $filtersBag;
    }

    public function preParseFilterValue(DashboardQueryManager $dashboardQueryManager, \Closure $next)
    {
        $dataBag = $dashboardQueryManager->getDataBag();

        $values = DB::table('custom_fields')
            ->where('name', 'Ticket Category')
            ->pluck('options')->all();

        $categoryValues = [];

        if (!empty($values)) {
            $options = json_decode($values[0], true);
            foreach ($options as $key => $value) {
                $categoryValues[] = $value;
            }
        }

        $dataBag->set('categoryValues', $categoryValues);

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
        $categoryValues = $dataBag->get('categoryValues');

        if ($filter === '0' || $filter === null) {
            $categoryIndex = 0;
        } else {
            $categoryIndex = array_search($filter, $categoryValues) + 1;
        }

        $dataBag->set('categoryIndex', $categoryIndex);

        $dashboardQueryManager->setDataBag($dataBag);

        return $next($dashboardQueryManager);
    }
}
