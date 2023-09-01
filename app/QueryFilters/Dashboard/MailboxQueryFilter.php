<?php

namespace App\QueryFilters\Dashboard;

use App\Support\Contracts\AbstractQueryFilter;
use App\Support\Dashboard\DashboardFiltersBag;
use Illuminate\Database\Eloquent\Builder;

class MailboxQueryFilter extends AbstractQueryFilter
{

    public function apply(Builder $query, $filterValue): Builder
    {
        if (intval($filterValue) != 0) {
            $query = $query->where('conversations.mailbox_id', $filterValue)
                ->with('mailbox.folders');
        }

        return $query;
    }

    public static function getFilterName(): string
    {
        return 'mailbox';
    }

    public function resolveFilterValue(DashboardFiltersBag $filtersBag): DashboardFiltersBag
    {
        $filtersBag->setFilterValue(static::getFilterName(), $this->request->input('mailbox'));

        return $filtersBag;
    }
}
