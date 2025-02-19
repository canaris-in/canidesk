<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\User;
use App\Mailbox;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
// use App\Support\Dashboard\DashboardQueryManager;
// use App\QueryConfigurations\{DashboardDefaultQueryConfiguration, TicketsQueryConfiguration};
// use App\QueryFilters\Dashboard\{ConversationTypeFilter,
//     DateRangeFilter,
//     DeletedTicketsFilter,
//     MailboxQueryFilter,
//     ProductFilter,
//     TicketCategoryFilter};
// use App\View\DashboardWidgets\{TicketsCountStatistics, WeeklyTicketsCountStatistics};


class DashboardController extends Controller
{
    // /**
    //  * @throws \Throwable
    //  */
    // public function index()
    // {
    //     $queryConfigurations = [
    //         'default' => DashboardDefaultQueryConfiguration::class,
    //         'tickets' => TicketsQueryConfiguration::class,
    //     ];

    //     $filtersConfigs = [
    //         DeletedTicketsFilter::class,
    //         TicketCategoryFilter::class,
    //         ProductFilter::class,
    //         MailboxQueryFilter::class,
    //         ConversationTypeFilter::class,
    //         DateRangeFilter::class
    //     ];

    //     $statisticsConfigs = [
    //         TicketsCountStatistics::class,
    //         WeeklyTicketsCountStatistics::class
    //     ];

    //     $dashboardQueryManager = DashboardQueryManager::make($queryConfigurations, null, $filtersConfigs, $statisticsConfigs)
    //         ->callHooksBeforeBuildQueryConfigurations()
    //         ->buildQueryConfigurations()
    //         ->callHooksAfterBuildQueryConfigurations()
    //         ->callHooksBeforeResolveQueryConfigurations()
    //         ->resolveQueryConfigurations()
    //         ->callHooksAfterResolveQueryConfigurations();


    //     /**
    //      * TODO: (@besrabasant)
    //      * - Refactor this so that it can access in blade templates directly.
    //      */
    //     $filters = $dashboardQueryManager->getFiltersBag()->getValues();

    //     $dataBag = $dashboardQueryManager->getDataBag();

    //     // Category Tickets
    //     return view('dashboard.dashboard',
    //         compact('filters') + $dataBag->all()
    //     );
    // }

    public function index(Request $request)
    {
        $today = Carbon::today();
        $fourDaysAgo = Carbon::today()->subDays(4);
        $sevenDaysAgo = Carbon::today()->subDays(7);
        $thirtyDaysAgo = Carbon::today()->subDays(30);
        $prev = false;
        $date_field = 'conversations.created_at';
        $date_field_to = '';
        // 1. Get all request parameter
        $filters = [
            'ticket' => $request->input('ticket'),
            'product' => $request->input('product'),
            'type' => $request->input('type'),
            'mailbox' => $request->input('mailbox'),
            'from' => $request->input('from'),
            'to' => $request->input('to')
        ];

        //Filter accourding timezon
        if ($request->has('from') && $request->has('to')) {
            $from = $request->input('from');
            $to =  $request->input('to');
        } else {
            $from = Carbon::today()->subDays(7);
            $to = Carbon::today();
            $filters['from'] = $from->format('Y-m-d');
            $filters['to'] = $to->format('Y-m-d');
        }

        if (!$date_field_to) {
            $date_field_to = $date_field;
        }

        if ($prev) {
            if ($from && $to) {
                $from_carbon = Carbon::parse($from);
                $to_carbon = Carbon::parse($to);

                $days = $from_carbon->diffInDays($to_carbon);

                if ($days) {
                    $from = $from_carbon->subDays($days)->format('Y-m-d');
                    $to = $to_carbon->subDays($days)->format('Y-m-d');
                }
            }
        }
        //Category
        $values = DB::table('custom_fields')
            ->where('name', 'Ticket Category')->first();

        $categoryValues = [];
        if (!empty($values)) {
            $options = json_decode($values->options, true);
            foreach ($options as $key => $value) {
                array_push($categoryValues, $value);
            }
        }

        //product
        $productValue = DB::table('custom_fields')
            ->where('name', 'Product')->first();

        $productValues = [];
        if (!empty($productValue->options)) {
            $options = json_decode($productValue->options, true);
            foreach ($options as $key => $productValue) {
                array_push($productValues, $productValue);
            }
        }

        $categoryIndex = '';
        $productIndex = '';

        if ($filters['ticket'] === '0' || $filters['ticket'] === null) {
            $categoryIndex = 0;
        } else {
            $categoryIndex = array_search($filters['ticket'], $categoryValues) + 1;
        }
        if ($filters['product'] === '0' || $filters['product'] === null) {
            $productIndex = 0;
        } else {
            $productIndex = array_search($filters['product'], $productValues) + 1;
        }

        // Unassigned count
        $queryCommon = Conversation::select();

        if ($filters['type'] != 0) {
            $queryCommon->where('conversations.type', $filters['type']);
        }
        if ($filters['mailbox'] != 0) {
            $queryCommon->where('conversations.mailbox_id', $filters['mailbox']);
        }
        if (!empty($from)) {
            $queryCommon->where($date_field, '>=', date('Y-m-d 00:00:00', strtotime($from)));
        }
        if (!empty($to)) {
            $queryCommon->where($date_field_to, '<=', date('Y-m-d 23:59:59', strtotime($to)));
        }
        $totalCountQuery = clone $queryCommon;
        $unassignedCountQuery = clone $queryCommon;
        $overdueCountQuery = clone $queryCommon;
        $unclosedCountQuery = clone $queryCommon;
        $closedCountQuery = clone $queryCommon;
        $holdTicketQuery = clone $queryCommon;

        $closedTicketChartQuery = clone $queryCommon;
        $closedCountChartQuery = clone $queryCommon;
        $slaTicketsChartQuery = clone $queryCommon;

        $totalCount = $totalCountQuery->where(function ($query) {
            $query->where('state', Conversation::STATE_PUBLISHED)
                ->where('status', '!=', Conversation::STATUS_SPAM);
        })->count();
        $unassignedCount = $unassignedCountQuery->where(function ($query) {
            $query->where('state', Conversation::STATE_PUBLISHED)
                ->where('status', '!=', Conversation::STATUS_CLOSED)
                ->whereNull('user_id');
        })->count();
        $overdueCount = $overdueCountQuery->where(function ($query) {
            $query->where('created_at', '<=', Carbon::now()->subDays(3))
                ->where('state', Conversation::STATE_PUBLISHED)
                ->Where('status', '!=', Conversation::STATUS_CLOSED);
        })->count();
        $unclosedCount = $unclosedCountQuery->where(function ($query) {
            $query->where('state', Conversation::STATE_PUBLISHED)
                ->where('status', '!=', Conversation::STATUS_CLOSED);
        })->count();
        $closedCount = $closedCountQuery->where(function ($query) {
            $query->where('state', Conversation::STATE_PUBLISHED)
                ->where('status', Conversation::STATUS_CLOSED);
        })->count();
        $holdTicket = $holdTicketQuery->where(function ($query) {
            $query->where('state', Conversation::STATE_PUBLISHED)
                ->where('status', Conversation::STATUS_PENDING);
        })->count();
        // For Weekly data
        $startDate = now()->startOfWeek();
        $endDate = now()->endOfWeek();
        $daysOfWeek = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
        ];

        //close ticket Category wise chart
        $indexes = [];
        for ($i = 1; $i <= count($categoryValues); $i++) {
            $indexes[] = $i;
        }
        $closedTicketChartCategory = $closedTicketChartQuery->whereHas('customFields', function ($query) use ($indexes) {
            $query->where('name', 'Ticket Category')
            ->where('conversation_custom_field.value', $indexes);
        });

        $closedTicketChart = $closedTicketChartCategory->where(function ($query) {
            $query->where('state', Conversation::STATE_PUBLISHED)
            ->where('status', Conversation::STATUS_CLOSED);
        });

        $closedTicketByDayOfWeek = $closedTicketChart
            ->selectRaw('DAYOFWEEK(created_at) AS day_of_week, COUNT(*) AS count')
            ->groupBy('day_of_week')
            ->get();

        $categoryTickets = array_fill(0, 7, 0);
        foreach ($closedTicketByDayOfWeek as $result) {
            // The day_of_week value is 1-based, so subtract 1 to get the correct index
            $dayOfWeek = $result->day_of_week - 1;
            $categoryTickets[$dayOfWeek] = $result->count;
        }
        //close ticket day of week wise chart
        $closedCountChart = $closedCountChartQuery->where(function ($query) {
            $query->where('state', Conversation::STATE_PUBLISHED)
                ->where('status', Conversation::STATUS_CLOSED);
        });

        $closedCountByDayOfWeek = $closedCountChart
            ->selectRaw('DAYOFWEEK(created_at) AS day_of_week, COUNT(*) AS count')
            ->groupBy('day_of_week')
            ->get();

        $tickets = array_fill(0, 7, 0);
        foreach ($closedCountByDayOfWeek as $result) {
            // The day_of_week value is 1-based, so subtract 1 to get the correct index
            $dayOfWeek = $result->day_of_week - 1;
            $tickets[$dayOfWeek] = $result->count;
        }
        //sla chart
        $slaTicketsChart = $slaTicketsChartQuery->where(function ($query) {
            $query->where('created_at', '<=', Carbon::now()->subDays(3))
                ->where('state', Conversation::STATE_PUBLISHED)
                ->Where('status', '!=', Conversation::STATUS_CLOSED);
        });
        $slaTicketsCount = $slaTicketsChart
            ->selectRaw('DAYOFWEEK(created_at) AS day_of_week, COUNT(*) AS count')
            ->groupBy('day_of_week')
            ->get();

        $sla = array_fill(0, 7, 0);
        foreach ($slaTicketsCount as $result) {
            // The day_of_week value is 1-based, so subtract 1 to get the correct index
            $dayOfWeek = $result->day_of_week - 1;
            $sla[$dayOfWeek] = $result->count;
        }
        return view('/dashboard/dashboard', compact('filters', 'categoryValues', 'productValues', 'totalCount', 'unassignedCount', 'overdueCount', 'unclosedCount', 'closedCount', 'holdTicket', 'tickets', 'sla','categoryTickets'));
    }
}
