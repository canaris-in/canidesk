<?php

namespace App\View\DashboardWidgets;

use App\Conversation;
use App\Folder;
use App\Support\Contracts\AbstractQueryConfiguration;
use App\Support\Dashboard\DashboardDataBag;
use App\Support\Dashboard\DashboardQueryManager;
use App\View\DashboardWidgets\Contracts\{AbstractDashboardStatistics, UsesQueryConfiguration};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TicketsCountStatistics extends AbstractDashboardStatistics implements UsesQueryConfiguration
{
    public function build(Builder $query): Builder
    {
        return $query->addSelect([
            'conversations.*',
            DB::raw('COUNT(*) as total_count'),
//            DB::raw('COUNT(CASE WHEN created_by_user_id IS NULL THEN 1 END) as unassigned_count'),
            DB::raw(sprintf("COUNT(CASE WHEN conversations.user_id IS NULL AND status <> %s THEN 1 END) as unassigned_count", Conversation::STATUS_CLOSED)),
            // DB::raw('COUNT(CASE WHEN closed_at IS NULL THEN 1 END) as overdue_count'),
            // DB::raw('COUNT(CASE WHEN created_at < ? AND closed_at IS NULL THEN 1 END) as overdue_count'),
            DB::raw('COUNT(CASE WHEN closed_at IS NULL THEN 1 END) as unclosed_count'),
            DB::raw('COUNT(CASE WHEN closed_at IS NOT NULL THEN 1 END) as closed_tickets_count'),
            DB::raw('COUNT(CASE WHEN closed_at IS NULL THEN 1 END) as unclosed_created_30_days_ago_count')
        ]);
    }

    public function useQueryConfiguration(): string
    {
        return 'default';
    }

    /**
     * @param AbstractQueryConfiguration $queryConfiguration
     * @param DashboardDataBag $dashboardDataBag
     * @param DashboardQueryManager $dashboardQueryManager
     * @throws \Throwable
     */
    public function resolve(
        AbstractQueryConfiguration $queryConfiguration,
        DashboardDataBag           $dashboardDataBag,
        DashboardQueryManager      $dashboardQueryManager)
    {
        $filters = $dashboardQueryManager->getFiltersBag();
        $results = $queryConfiguration->getResults();

        $totalUnassignedCount = intval($filters->getFilterValue('mailbox')) === 0 || is_null($results->mailbox) ?
            $results->unassigned_count
            :
            $results->mailbox->folders->where('type', Folder::TYPE_UNASSIGNED)->where('user_id', null)->first()->total_count;

        $dashboardDataBag->set('totalCount', $results->total_count);
        $dashboardDataBag->set('unassignedCount', $totalUnassignedCount);
        $dashboardDataBag->set('overdueCount', 0);
        $dashboardDataBag->set('unclosedCount', $results->unclosed_count);
        $dashboardDataBag->set('closedCount', $results->closed_tickets_count);
        $dashboardDataBag->set('unclosedCreated30DaysAgoCount', $results->unclosed_created_30_days_ago_count);
    }
}
