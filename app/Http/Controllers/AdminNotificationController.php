<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function dismiss(Request $request, AdminNotification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403);
        }

        $notification->markAsRead();

        return back();
    }

    public function dismissAll(Request $request)
    {
        $request->user()
            ->adminNotifications()
            ->unread()
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications dismissed.');
    }
}
