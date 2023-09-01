<?php

namespace App\QueryFilters\Dashboard;

use App\Folder;
use App\Support\Contracts\AbstractQueryFilter;
use App\Support\Dashboard\DashboardFiltersBag;
use Illuminate\Database\Eloquent\Builder;

class DeletedTicketsFilter extends AbstractQueryFilter
{
    public function apply(Builder $query, $filterValue): Builder
    {
        if ($filterValue === false) {
            return $query->join('folders', 'folders.id', '=', 'conversations.folder_id')
                ->where('folders.type', '!=', Folder::TYPE_DELETED);
        }
        return $query;
    }

    public static function getFilterName(): string
    {
        return 'deleted';
    }

    public function resolveFilterValue(DashboardFiltersBag $filtersBag): DashboardFiltersBag
    {
        $filtersBag->setFilterValue(static::getFilterName(), false);

        return $filtersBag;
    }
}
