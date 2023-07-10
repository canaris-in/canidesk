<?php

namespace App\Http\Controllers;

use App\Notifications\WebsiteNotification;
use Illuminate\Http\Request;
use App\Thread;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ViewNotificationController extends Controller
{

    const WEBSITE_NOTIFICATIONS_PAGE_SIZE = 40;
    const WEBSITE_NOTIFICATIONS_PAGE_PARAM = 'wp_page';
    public function index($id){

        $auth_user = auth()->user();
        $data = [];

        $threads = [];
        $thread_ids = [];
        $notifications = $auth_user->notifications()->paginate(self::WEBSITE_NOTIFICATIONS_PAGE_SIZE, ['*'], self::WEBSITE_NOTIFICATIONS_PAGE_PARAM, request()->wn_page);

        foreach ($notifications as $notification) {
            if (!empty($notification->data['thread_id'])) {
                $thread_ids[] = $notification->data['thread_id'];
            }
        }
        if ($thread_ids) {
            $threads = Thread::whereIn('id', $thread_ids)
                ->with('conversation')
                ->with('created_by_user')
                ->with('created_by_customer')
                ->with('user')
                ->get();
        }

        foreach ($notifications as $notification) {
            $conversation_number = '';
            if (!empty($notification->data['number'])) {
                $conversation_number = $notification->data['number'];
            }

            $thread = null;
            $user = null;
            $created_by_user = null;
            $created_by_customer = null;

            if (!empty($notification->data['thread_id'])) {
                $thread = $threads->firstWhere('id', $notification->data['thread_id']);
                if (empty($thread)) {
                    continue;
                }
                if ($thread->user_id) {
                    $user = $thread->user;
                }
                if ($thread->created_by_user_id) {
                    $created_by_user = $thread->created_by_user_id;
                }
                if ($thread->created_by_customer_id) {
                    $created_by_customer = $thread->created_by_customer_id;
                }
            } else {
                continue;
            }

            $last_thread_body = $thread->body;
            $conversation = $thread->conversation;

            if (empty($conversation)) {
                continue;
            }

            $data[] = [
                'notification'        => $notification,
                'created_at'          => $notification->created_at,
                'conversation'        => $conversation,
                'thread'              => $thread,
                'last_thread_body'    => $last_thread_body,
                'user'                => $user,
                'created_by_user'     => $created_by_user,
                'created_by_customer' => $created_by_customer,
            ];
        }

        return view('/users/view_notification',['web_notifications_info_data' => $data]);
    }
}
