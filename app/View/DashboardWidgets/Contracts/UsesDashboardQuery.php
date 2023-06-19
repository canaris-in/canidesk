<?php

namespace App\View\DashboardWidgets\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

interface UsesDashboardQuery
{
    /**
     * @param Builder $query
     * @return Builder|Expression|Expression[]
     */
    public function resolve(Builder $query);
}
