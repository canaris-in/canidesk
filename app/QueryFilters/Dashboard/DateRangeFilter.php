<?php

namespace App\QueryFilters\Dashboard;

use App\Support\Contracts\AbstractQueryFilter;
use App\Support\Dashboard\DashboardFiltersBag;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DateRangeFilter extends AbstractQueryFilter
{
    protected $date_field = 'conversations.created_at';
    protected $date_field_to = '';

    protected $prev = false;

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

        if (!$this->date_field_to) {
            $this->date_field_to = $this->date_field;
        }

        if (!empty($from)) {
            $query = $query->where($this->date_field, '>=', Carbon::parse($from)->startOfDay()->format('Y-m-d H:i:s'));
        }

        if (!empty($to)) {
            $query = $query->where($this->date_field_to, '<=', Carbon::parse($to)->endOfDay()->format('Y-m-d H:i:s'));
        }

        return $query;
    }

    public function resolveFilterValue(DashboardFiltersBag $filtersBag): DashboardFiltersBag
    {
        if ($this->request->has('from') && $this->request->has('to')) {
            $from = Carbon::parse($this->request->input('from'));
            $to = Carbon::parse($this->request->input('to'));
        } else {
            $from = Carbon::today()->subDays(7);
            $to = Carbon::today();
        }

        if ($this->prev) {
            $days = $from->diffInDays($to);

            if ($days) {
                $from = $from->subDays($days);
                $to = $to->subDays($days);
            }
        }

        $filtersBag->setFilterValue('from', $from->format('Y-m-d'));
        $filtersBag->setFilterValue('to', $to->format('Y-m-d'));

        return $filtersBag;
    }
}
