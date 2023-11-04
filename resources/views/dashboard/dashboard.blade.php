@extends('layouts.app')

@section('content')
    <i class="glyphicon glyphicon-filter filter-trigger"></i>
    <div class="rpt-header">
        <form action="{{ route('filter') }}" method="GET" class="top-form">
            <div class="rpt-filters">


                {{-- <div class="rpt-filter">
                    <label>
                        {{ __('Tickets Category') }}
                    </label>
                    <select class="form-control" name="ticket">
                        <option value="0">{{__('All')}}</option>
                        @foreach ($categoryValues as $category)
                            <option value="{{ $category }}" {{ $filters['ticket'] === $category ? 'selected' : '' }}>
                                {{ $category }}</option>
                        @endforeach
                    </select>
                </div> --}}
                {{-- <div class="rpt-filter">
                    <label>
                        {{ __('Product') }}
                    </label>
                    <select class="form-control" name="product">
                        <option value="0">{{__('All')}}</option>
                        @foreach ($productValues as $product)
                            <option value="{{ $product }}" {{ $filters['product'] === $product ? 'selected' : '' }}>
                                {{ $product }}</option>
                        @endforeach
                    </select>
                </div> --}}
                <div class="rpt-filter">
                    <label>
                        {{ __('Type') }}
                    </label>
                    <select class="form-control" name="type">
                        <option value="0">{{__('All')}}</option>
                        <option value="{{ App\Conversation::TYPE_EMAIL }}"
                            {{ $filters['type'] == App\Conversation::TYPE_EMAIL ? 'selected' : '' }}>{{ __('Email') }}
                        </option>
                        <option value="{{ App\Conversation::TYPE_CHAT }}"
                            {{ $filters['type'] == App\Conversation::TYPE_CHAT ? 'selected' : '' }}>{{ __('Chat') }}
                        </option>
                        <option value="{{ App\Conversation::TYPE_PHONE }}"
                            {{ $filters['type'] == App\Conversation::TYPE_PHONE ? 'selected' : '' }}>{{ __('Phone') }}
                        </option>
                    </select>
                </div>
                <div class="rpt-filter">
                    <label>
                        {{ __('Mailbox') }}
                    </label>
                    <select class="form-control" name="mailbox">
                        <option value="0">{{__('All')}}</option>
                        @foreach (Auth::user()->mailboxesCanView(true) as $mailbox)
                            <option value="{{ $mailbox->id }}"
                                {{ $filters['mailbox'] == $mailbox->id ? 'selected' : '' }}>{{ $mailbox->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="rpt-filter">
                    <label class="mobile-label">{{ __('Date Range') }}</label>
                    <nobr><input type="date" name="from" class="form-control rpt-filter-date" id="from"
                            value="{{ $filters['from'] }}" />-<input type="date" name="to" id="to"
                            class="form-control rpt-filter-date" value="{{ $filters['to'] }}" /></nobr>
                </div>

                <div class="rpt-filter" data-toggle="tooltip" title="{{ __('Refresh') }}">
                    <button class="btn btn-primary" id="rpt-btn-loader" onclick="refreshPage()" type="submit"><i
                            class="glyphicon glyphicon-refresh"></i></button>
                </div>

            </div>

        </form>
    </div>
    <div class="container-fluid color" style="padding: 0 20px;margin-bottom: 3em;">
        <div class="row text-center" style="margin-top: 6rem;">
            <div class="row-text-center1">
                <div class="col-md-4">
                    <p class="stat-options">{{__('Total Tickets')}}</p>
                    <h1 class="stat-values">{{ $totalCount }}</h1>
                </div>
                <div class="col-md-4">
                    <p class="stat-options">{{__('Unassigned Tickets')}}</p>
                    <h1 class="stat-values">{{ $unassignedCount }}</h1>
                </div>
                <div class="col-md-4">
                    <p class="stat-options">{{__('Overdue Tickets')}}</p>
                    <h1 class="stat-values">{{ $overdueCount }}</h1>
                </div>
            </div>
        </div>

        <div class="row text-center" style="margin-top: 0rem;">
            <div class="row-text-center1">
                <div class="col-md-4">
                    <p class="stat-options">{{__('Open Tickets')}}</p>
                    <h1 class="stat-values">{{ $unclosedCount }}</h1>
                </div>
                <div class="col-md-4">
                    <p class="stat-options">{{__('Close Tickets')}}</p>
                    <h1 class="stat-values">{{ $closedCount }}</h1>
                </div>
                <div class="col-md-4">
                    <p class="stat-options">{{__('Pending Tickets')}}</p>
                    <h1 class="stat-values">{{ $holdTicket }}</h1>
                </div>
            </div>
        </div>


        <div class="donut-container">
            @include('dashboard.widgets.donut_chart')
            @include('dashboard.widgets.average_resolved_tickets')
        </div>
        <div class="bar-container">
            @include('dashboard.widgets.weekly_resolved_tickets')
            @include('dashboard.widgets.average_time_tickets_sla')
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .rpt-header {
    background-color: #deecf9;
    padding: 12px 18px;
    line-height: 30px;
    overflow: auto;
    box-shadow: -3px 13px 19px -2px #00000057;
}
.dm .rpt-header {
    background-color: transparent;
}
@media screen and (max-width: 600px) {
    .dm .rpt-header {
        background-color: #12131F !important;
    }
}
.rpt-title {
    color: #2a3b47;
    font-size: 20px;
    font-weight: 400;
    display: inline-block;
}
.rpt-filters {
    display: inline-block;
    float: right;
}
#rpt-filters .rpt-filter .form-control {
    display: inline-block;
    width: auto;
    max-width: 120px;
    margin-left: 5px;
}
.rpt-views-trigger .btn {
    padding-left: 8px!important;
    padding-right: 8px!important;
}
.rpt-views-trigger .input-group {
    margin: 6px 11px 3px 11px;
    width: 200px;
    line-height: 28px;
}
.rpt-views-trigger li {
    position: relative;
}
.rpt-views-trigger li a {
    margin-left: 27px;
    padding-left: 3px;
}
.rpt-view-delete {
    position: absolute;
    left: 10px;
    top: 10px;
    font-size: 11px;
    cursor: pointer;
}
.rpt-filter,
.rpt-views-trigger {
    display: inline-block;
    margin-right: 15px;
}
.rpt-filter .glyphicon-refresh {
    top: 3px;
}
.rpt-filter:last-child {
    margin-right: 0;
}
.rpt-filter:nth-last-child(2) {
    margin-right: 5px;
}
@media (max-width:1100px) {
    #rpt-filters {
        float: none;
        margin-top: 2px;
        margin-bottom: 7px;
    }
    .rpt-filter {
        margin-top: 10px;
    }
}

        .content {
            margin-top: 0;
        }

        .top-form {
            display: flow-root;
            height: auto;
            align-items: center;
            justify-content: space-evenly;
            background: #deecf9;
        }

        .dm .top-form {
            background: transparent;
        }

        .dm .rpt-header {
            background-color: transparent;
        }

        .rpt-header {
            background-color: #deecf9;
            padding: 12px 18px;
            line-height: 30px;
            overflow: auto;
            box-shadow: -3px 13px 19px -2px #00000057;
        }

        .opn-menu {
            padding: 12px 18px;
            width: 75%;
            left: 25%;
        }

        .circle {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
            position: relative;
            top: 22px;
            left: -15px;
        }

        .circle-green {
            background-color: #89F81B;
        }

        .circle-red {
            background-color: red;
        }

        .circle-blue {
            background-color: #173292;
        }

        .circle-cyan {
            background-color: rgba(46,165,251, 0.7);
        }
        .dm .form-control {
            display: inline;
            width: 140px;
            min-inline-size: max-content;
        }

        .form-control {
            display: inline;
            width: 140px;
            min-inline-size: max-content;
        }

        .dm hr {
            border: 1px solid #eee;
        }

        hr {
            border: 1px solid black;
        }

        .dm .donut-container .donut-chart {
            display: flex;
            flex: 1;
            align-items: center;
            gap: 5rem;
            background: #1D1C24;
            padding: 4px;
            border-radius: 4px;
            height: 267px;
            width: 100%;
        }

        .donut-container .donut-chart {
            display: flex;
            flex: 1;
            align-items: center;
            gap: 5rem;
            background: #eeeeee;
            padding: 4px;
            border-radius: 4px;
            height: 267px;
            width: 100%;
        }

        .donut-container .donut-chart .donut-chart-lable {
            display: flex;
            flex: 1;
            align-items: center;
            gap: 5px;
        }

        .donut-container .donut-chart .donut-chart-box {
            background-color: plum;
            height: 40px;
            width: 40px;
            border-radius: 4px;

        }

        .donutp {
            display: table-header-group;
        }

        .dm .horizontalChart {
            display: flex;
            flex: 1;
            align-items: center;
            gap: 7rem;
            background: #1D1C24;
            padding: 4px;
            border-radius: 4px;
            height: 267px;
            width: 100%;
        }

        .horizontalChart {
            display: flex;
            flex: 1;
            align-items: center;
            gap: 7rem;
            background: #eeeeee;
            padding: 4px;
            border-radius: 4px;
            height: 267px;
            width: 100%;
        }

        .dm .bar-container .barChart {
            display: flex;
            flex: 1;
            align-items: center;
            gap: 7rem;
            background: #1D1C24;
            padding: 4px;
            border-radius: 4px;
            height: 267px;
            justify-content: center;
            width: 100%;
        }

        .bar-container .barChart {
            display: flex;
            flex: 1;
            align-items: center;
            gap: 7rem;
            background: #eeeeee;
            padding: 4px;
            border-radius: 4px;
            height: 267px;
            justify-content: center;
            width: 100%;
        }

        .dm .bar-container .lineChart {
            display: flex;
            flex: 1;
            align-items: center;
            gap: 7rem;
            background: #1D1C24;
            padding: 4px;
            border-radius: 4px;
            height: 267px;
            width: 100%;
        }

        .bar-container .lineChart {
            display: flex;
            flex: 1;
            align-items: center;
            gap: 7rem;
            background: #eeeeee;
            padding: 4px;
            border-radius: 4px;
            height: 267px;
            width: 100%;
        }

        input,
        button,
        select,
        textarea {
            color: #1D1C24
        }


        /* its my code for external coonent*/
        .dm .donut-container {
            max-width: 100%;
            display: flex;
            flex: 50%;

        }

        .donut-container {
            max-width: 100%;
            display: flex;
            flex: 50%;
        }


        .dm .bar-container {
            max-width: 100%;
            display: flex;
            flex: 50%;
        }

        .bar-container {
            display: flex;
            flex: 50%;
            max-width: 100%;
        }

        .mobile-label {
            display: none;
        }

        .filter-trigger {
            display: none;
        }

        /* its my code for external coonent*/
        /**
             * Update: 06/06/23
             * I am over-writing your code
              */

        @media (max-width: 600px) {
            .dm .donut-container {
                display: flex;
                flex-direction: column;
            }

            .donut-container {
                display: flex;
                flex-direction: column;

            }

            .rpt-filter {
                display: flex;
                flex-direction: column;
                margin-bottom: 1.5em;
            }

            .rpt-header {
                position: absolute;
                z-index: 9;
                height: 100vh;
                width: 0;
                transition: 200ms;
                padding: 0;
                left: 100%;
                box-shadow: -3px 13px 19px -2px #00000057;
            }

            .opn-menu {
                padding: 12px 18px;
                width: 75%;
                left: 25%;
            }

            .mobile-label {
                display: block;
            }


            .dm .bar-container {
                display: flex;
                flex-direction: column;
            }

            .bar-container {
                display: flex;
                flex-direction: column;
            }


            .row-text-center1 {
                display: flex;
                flex-direction: row;
                margin-left: 0px;
            }


            .stat-options {
                font-size: 15px;
                max-width: 100%;
                margin-top: 20px;
            }

            .stat-values {
                font-size: 35px;
                max-width: 100%;
            }

            .donut-container {
                margin-top: 3rem;
            }

            .filter-trigger {
                display: block;
                color: #0078d7;
                font-size: 15px;
                position: absolute;
                right: 0;
                background: white;
                padding: 0.5em;
                top: 50px;
                box-shadow: 4px 2px 8px 1px #0000008f;
                border: 1px solid #0078d7;
                transition: 200ms;
            }

            .opn-filter {
                right: 75%;
            }

        }
    </style>
@endpush

@push('vendor_libraries')
    <script src="/public/js/chart.js"></script>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('click', '.filter-trigger', function() {
                $('.rpt-header').toggleClass('opn-menu');
                $('.filter-trigger').toggleClass('opn-filter');
                if ($('.filter-trigger').hasClass('glyphicon-remove')) {
                    $('.filter-trigger').removeClass('glyphicon-remove');
                    $('.filter-trigger').addClass('glyphicon-filter');
                } else {
                    $('.filter-trigger').addClass('glyphicon-remove');
                    $('.filter-trigger').removeClass('glyphicon-filter');
                }
            });

        });

        function refreshPage() {
            location.reload();
        }


        $(document).ready(function() {
            var today = new Date().toISOString().split('T')[0];
            $('#from').attr('max', today);
            $('#to').attr('max', today);
        });
    </script>
@endpush
