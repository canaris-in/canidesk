<?php

namespace App\QueryFilters\Dashboard;

use Illuminate\Database\Eloquent\Builder;

class ConversationTypeFilter extends AbstractQueryFilter
{
    public function apply(Builder $query, $filterValue): Builder
    {
        return $query->where('conversations.type', $filterValue);
    }

    public static function getFilterName(): string
    {
       return 'type';
    }
}
