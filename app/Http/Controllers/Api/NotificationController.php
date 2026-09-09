<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // ✅ Get Notifications (clean response)
    public function getNotifications()
    {
        try {
            $notifications = Notification::where('notifiable_type', User::class)
                ->where('notifiable_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($n) {
                    $data = json_decode($n->data, true);
                    return [
                        'id'         => $n->id,
                        'message'    => $data['message'] ?? $data['data'] ?? '',
                        'read_at'    => $n->read_at,   // null = unread
                        'created_at' => $n->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // ✅ Mark All Read
    public function markAllRead()
    {
        try {
            Notification::where('notifiable_type', User::class)
                ->where('notifiable_id', Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ Mark Single Read
    public function markOneRead($id)
    {
        try {
            Notification::where('id', $id)
                ->where('notifiable_type', User::class)
                ->where('notifiable_id', Auth::id())
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ Unread Count
    public function unreadCount()
    {
        $count = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}
