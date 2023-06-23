<?php

namespace App\View\DashboardWidgets\Contracts;

use App\QueryConfigurations\DashboardDefaultQueryConfiguration;
use App\Support\Contracts\AbstractQueryConfiguration;
use App\Support\Dashboard\DashboardDataBag;
use App\Support\Dashboard\DashboardQueryManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

interface UsesQueryConfiguration
{
    public function useQueryConfiguration(): string;

    /**
     * @param Builder $query
     * @return Builder|Expression|Expression[]
     */
    public function build(Builder $query);
    public function resolve(
        AbstractQueryConfiguration $queryConfiguration,
        DashboardDataBag                   $dashboardDataBag,
        DashboardQueryManager              $dashboardQueryManager
    );

}
