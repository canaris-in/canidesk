<?php

namespace App\QueryConfigurations;

use App\Conversation;
use App\Support\Contracts\AbstractQueryConfiguration;
use App\Support\Dashboard\DashboardDataBag;
use App\Support\Dashboard\DashboardFiltersBag;
use App\Support\Dashboard\DashboardQueryManager;
use App\Support\Dashboard\DashboardStatisticsConfiguration;
use Illuminate\Database\Eloquent\Builder;

class DashboardDefaultQueryConfiguration extends AbstractQueryConfiguration
{
    public function buildQuery(
        DashboardDataBag      $dashboardDataBag,
        DashboardFiltersBag   $dashboardFiltersBag,
        DashboardQueryManager $dashboardQueryManager
    ): Builder
    {
        $query = Conversation::query();

        $query = $query->where('state', '!=', Conversation::STATE_DRAFT)
            ->where('threads_count', '>', 0);

        $data = $dashboardDataBag->get(['categoryIndex', 'productIndex'], ['categoryIndex' => '', 'productIndex' => '']);

        $categoryIndex = $data['categoryIndex'];

        $productIndex = $data['productIndex'];

        if (!empty($categoryIndex) || !empty($productIndex)) {
            $query = $query->join('conversation_custom_field', 'conversations.id', '=', 'conversation_custom_field.conversation_id')
                ->join('custom_fields', 'conversation_custom_field.custom_field_id', '=', 'custom_fields.id')
                ->where('custom_fields.name', 'Ticket Category')
                ->where('conversation_custom_field.value', $categoryIndex)
                ->orWhere('custom_fields.name', 'Product')
                ->where('conversation_custom_field.value', $productIndex)
                ->select('conversations.*');
        }

        return $query;
    }

    public function resolveQuery(
        DashboardStatisticsConfiguration $statisticsConfiguration,
        DashboardDataBag                 $dashboardDataBag,
        DashboardQueryManager            $dashboardQueryManager
    )
    {
        return $this->query->first();
    }
}
