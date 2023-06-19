<?php

namespace App\QueryFilters\Dashboard;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DateRangeFilter extends AbstractQueryFilter
{

    public static function getFilterName(): array
    {
        return [
            'from',
            'to'
        ];
    }

    /**
     * @inheritDoc
     */
    public function apply(Builder $query, $filterValue): Builder
    {
        $from = $filterValue['from'];
        $to = $filterValue['to'];
        $date_field = 'conversations.created_at';

        if (!empty($from)) {
            $query = $query->where($date_field, '>=', Carbon::parse($from)->startOfDay()->format('Y-m-d H:i:s'));
        }

        if (!empty($to)) {
            $query = $query->where($date_field, '<=', Carbon::parse($to)->endOfDay()->format('Y-m-d H:i:s'));
        }

        return $query;
    }
}
