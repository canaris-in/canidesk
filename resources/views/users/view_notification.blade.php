@extends('layouts.app')
@section('content')
    <div class="layout-2col layout-2col-settings">
        @php
            $web_notifications_info = Auth::user()->getWebsiteNotificationsInfo();
        @endphp
        <div class="sidebar-2col">
            <div class="sidebar-title">
                {{ __('Notifications') }}
            </div>
            <div class="sidebar-title reports-sidebar-title">
                <span class="glyphicon glyphicon-bell"></span>
                {{ __('Notifications') }}
            </div>
        </div>
        <div class="content-2col">
            <div class="section-heading">
                {{ __('Notifications') }}
                <small class="web-notifications-count  @if (!(int) $web_notifications_info['unread_count']) hidden @endif"
                    title="{{ __('Unread Notifications') }}" data-toggle="tooltip">
                    @if ($web_notifications_info['unread_count'])
                        {{ $web_notifications_info['unread_count'] }}
                    @endif
                </small>
                <a href="#" class="web-notifications-mark-read @if (!(int) $web_notifications_info['unread_count']) hidden @endif"
                    data-loading-text="{{ __('Processing') }}…">
                    {{ __('Mark all as read') }}
                </a>
            </div>
            <div class="container_settings row-container form-container top-margin">
                <div class="inner-container row">
                    <div class="col-xs-12">

                        @if (empty($web_notifications_info_data))
                            <ul class="web-notifications-list">
                                <div class="text-center margin-top-40 margin-bottom-40">
                                    <i class="glyphicon glyphicon-bullhorn icon-large"></i>
                                    <p class="block-help text-large">
                                        {{ __('Notifications will start showing up here soon') }}
                                    </p>
                                    <a
                                        href="{{ route('users.notifications', ['id' => Auth::user()->id]) }}">{{ __('Update your notification settings') }}</a>
                                </div>
                            </ul>
                        @else
                            @foreach ($web_notifications_info_data as $web_notification_data)
                                @if (
                                    $loop->first ||
                                        \App\User::dateFormat($web_notifications_info_data[$loop->index - 1]['created_at'], 'M j, Y') !=
                                            \App\User::dateFormat($web_notification_data['created_at'], 'M j, Y'))
                                    @php
                                        $notification_date = \App\User::dateFormat($web_notification_data['created_at'], 'M j, Y');
                                    @endphp
                                    <li class="web-notification-date" data-date="{{ $notification_date }}">
                                        @if ($notification_date == \App\User::dateFormat(\Carbon\Carbon::now(), 'M j, Y'))
                                            {{ __('Today') }}
                                        @else
                                            {{ $notification_date }}
                                        @endif
                                    </li>
                                @endif
                                <li class="web-notification @if (empty($web_notification_data['notification']->read_at)) is-unread @endif"
                                    data-notification_id="{{ $web_notification_data['notification']->id }}">
                                    @php
                                        $conv_params = [];
                                        if (!$web_notification_data['notification']->read_at) {
                                            $conv_params['mark_as_read'] = $web_notification_data['notification']->id;
                                        }
                                    @endphp
                                    <a href="{{ $web_notification_data['conversation']->url(null, $web_notification_data['thread']->id, $conv_params) }}"
                                        title="{{ __('View conversation') }}">
                                        <div class="web-notification-img">
                                            @include('partials/person_photo', [
                                                'person' => $web_notification_data['thread']->getPerson(true),
                                            ])
                                        </div>
                                        <div class="web-notification-msg">
                                            <div class="web-notification-msg-header">
                                                {!! $web_notification_data['thread']->getActionDescription(
                                                    $web_notification_data['conversation']->number,
                                                    true,
                                                    Auth::user(),
                                                ) !!}
                                            </div>
                                            <div class="web-notification-msg-preview">
                                                {{ App\Misc\Helper::textPreview($web_notification_data['last_thread_body']) }}
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            @if (!empty($notifications))
            <div class="customers-pager">
                @if ($notifications->links())
                    {{-- First Page Link --}}
                    <a href="{{ $notifications->url(1) }}"
                        class="pager-nav pager-first glyphicon glyphicon-backward @if ($notifications->currentPage() <= 2) disabled @endif"
                        data-page="1" title="{{ __('First Page') }}"></a>

                    {{-- Previous Page Link --}}
                    <a href="{{ $notifications->previousPageUrl() }}"
                        class="pager-nav pager-prev glyphicon glyphicon-triangle-left @if ($notifications->onFirstPage()) disabled @endif"
                        data-page="{{ $notifications->currentPage() + 1 }}" title="{{ __('Previous Page') }}"></a>

                    {{-- Next Page Link --}}
                    <a href="{{ $notifications->nextPageUrl() }}"
                        class="pager-nav pager-next glyphicon glyphicon-triangle-right @if (!$notifications->hasMorePages()) disabled @endif"
                        data-page="{{ $notifications->currentPage() + 1 }}" title="{{ __('Next Page') }}"></a>

                    {{-- Last Page Link --}}
                    <a href="{{ $notifications->url($notifications->lastPage()) }}"
                        class="pager-nav pager-last glyphicon glyphicon-forward @if ($notifications->currentPage() >= $notifications->lastPage() - 1) disabled @endif"
                        data-page="{{ $notifications->lastPage() }}" title="{{ __('Last Page') }}"></a>
                @endif
            </div>
            @endif
        </div>
    </div>
    <style>
        .layout-2col-settings {
            margin-top: -19px;
        }

        .reports-sidebar-title {
            font-size: 15px;
        }

        li {
            list-style-type: none;
        }
    </style>
@endsection
