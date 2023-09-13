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
        if (!empty($values)) {
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

        $query = Conversation::select(
            DB::raw('COUNT(*) as total_count'),
            DB::raw('COUNT(CASE WHEN folder_id = 1 THEN 1 ELSE NULL END) as unassigned_count'),
            DB::raw('COUNT(CASE WHEN DATE_SUB(created_at, INTERVAL 3 DAY) AND folder_id != 4 AND folder_id != 6 THEN 1 END) as overdue_count'),
            DB::raw('COUNT(CASE WHEN (status = 1 OR status = 2) AND folder_id != 6 THEN 1 ELSE NULL END) as unclosed_count'),
            DB::raw('COUNT(CASE WHEN folder_id = 4 THEN 1 ELSE NULL END) as closed_tickets_count'),
            DB::raw('COUNT(CASE WHEN status = 2 AND folder_id != 6 THEN 1 ELSE NULL END) as hold_ticket')
        );

        if ($filters['type'] != 0) {
            $query->where('conversations.type', $filters['type']);
        }
        if ($filters['mailbox'] != 0) {
            $query->where('conversations.mailbox_id', $filters['mailbox']);
        }
        if (!empty($from)) {
            $query->where($date_field, '>=', date('Y-m-d 00:00:00', strtotime($from)));
        }
        if (!empty($to)) {
            $query->where($date_field_to, '<=', date('Y-m-d 23:59:59', strtotime($to)));
        }

        $results = $query->first();

        $totalCount = $results->total_count;
        $unassignedCount = $results->unassigned_count;
        $overdueCount = $results->overdue_count;
        $unclosedCount = $results->unclosed_count;
        $closedCount = $results->closed_tickets_count;
        $holdTicket = $results->hold_ticket;

        // For Weekly data
        $startDate = now()->startOfWeek();
        $endDate = now()->endOfWeek();
        $daysOfWeek = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
        ];


        $closedTickets = Conversation::selectRaw('DAYNAME(closed_at) as day, COUNT(*) as count')
            ->whereBetween('closed_at', [$startDate, $endDate])
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

        $tickets = [];
        foreach ($daysOfWeek as $day) {
            $tickets[$day] = $closedTickets[$day] ?? 0;
        }

        return view('/dashboard/dashboard', compact('filters', 'categoryValues', 'productValues', 'totalCount', 'unassignedCount', 'overdueCount', 'unclosedCount', 'closedCount', 'holdTicket', 'tickets'));
    }
}
