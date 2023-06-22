<?php

namespace App\Support\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface QueryFilterStageContract
{
    /**
     * @param Builder $query
     * @param mixed $filterValue
     * @return Builder
     */
    public function apply(Builder $query, $filterValue): Builder;
}
