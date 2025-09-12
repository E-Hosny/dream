<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * جلب جميع إشعارات المستخدم
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 10);
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? 'unknown',
                    'title' => $notification->data['title'] ?? 'إشعار',
                    'message' => $notification->data['message'] ?? '',
                    'icon' => $notification->data['icon'] ?? 'info',
                    'color' => $notification->data['color'] ?? 'gray',
                    'action_url' => $notification->data['action_url'] ?? null,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->toISOString(),
                    'time_ago' => $notification->created_at->diffForHumans(),
                    'is_read' => !is_null($notification->read_at),
                ];
            }),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'last_page' => $notifications->lastPage(),
            ],
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * جلب عدد الإشعارات غير المقروءة
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * جلب آخر الإشعارات (للعرض في navbar)
     */
    public function recent(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 5);
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? 'unknown',
                    'title' => $notification->data['title'] ?? 'إشعار',
                    'message' => $notification->data['message'] ?? '',
                    'icon' => $notification->data['icon'] ?? 'info',
                    'color' => $notification->data['color'] ?? 'gray',
                    'action_url' => $notification->data['action_url'] ?? null,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->toISOString(),
                    'time_ago' => $notification->created_at->diffForHumans(),
                    'is_read' => !is_null($notification->read_at),
                ];
            }),
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * وضع علامة مقروء على إشعار واحد
     */
    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'الإشعار غير موجود'
            ], 404);
        }

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'message' => 'تم وضع علامة مقروء على الإشعار',
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * وضع علامة مقروء على جميع الإشعارات
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'تم وضع علامة مقروء على جميع الإشعارات',
            'unread_count' => 0,
        ]);
    }

    /**
     * حذف إشعار
     */
    public function destroy($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'الإشعار غير موجود'
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الإشعار بنجاح',
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * حذف جميع الإشعارات المقروءة
     */
    public function deleteRead()
    {
        $user = Auth::user();
        $user->readNotifications()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف جميع الإشعارات المقروءة',
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }
}
