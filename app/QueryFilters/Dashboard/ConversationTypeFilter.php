<?php

namespace App\QueryFilters\Dashboard;

use App\Support\Contracts\AbstractQueryFilter;
use App\Support\Dashboard\DashboardFiltersBag;
use Illuminate\Database\Eloquent\Builder;

class ConversationTypeFilter extends AbstractQueryFilter
{
    public function apply(Builder $query, $filterValue): Builder
    {
        if (!empty($filterValue)) {
            $query =  $query->where('conversations.type', $filterValue);
        }
        return $query;
    }

    public static function getFilterName(): string
    {
        return 'type';
    }

    public function resolveFilterValue(DashboardFiltersBag $filtersBag): DashboardFiltersBag
    {
        $filtersBag->setFilterValue(static::getFilterName(), $this->request->input('type'));

        return $filtersBag;
    }
}
