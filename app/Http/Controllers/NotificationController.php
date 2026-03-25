<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\Notification;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Add this for DB queries
use Jenssegers\Agent\Agent;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Notification::where('user_id', $user->id);

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('read')) {
            $isRead = $request->read === 'true' || $request->read === '1';
            $query->where('is_read', $isRead);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => Notification::where('user_id', $user->id)->count(),
            'unread' => Notification::where('user_id', $user->id)->where('is_read', false)->count(),
            'by_type' => Notification::where('user_id', $user->id)
                ->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get(),
        ];

        return view('notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Display the specified notification.
     */
    public function show(Notification $notification)
    {
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to notification.');
        }

        // Mark as read if not already read
        if (!$notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now()
            ]);

            // Log the activity
            $agent = new Agent();
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'view_notification',
                'module' => 'notification',
                'description' => 'Viewed notification: ' . substr($notification->message, 0, 50),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
            ]);
        }

        // Redirect to the notification link
        if ($notification->link) {
            return redirect($notification->link);
        }

        return redirect()->route('notifications.index');
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        $agent = new Agent();
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'mark_notification_read',
            'module' => 'notification',
            'description' => 'Marked notification as read',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();

        $count = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        $agent = new Agent();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'mark_all_read',
            'module' => 'notification',
            'description' => "Marked {$count} notifications as read",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
        ]);

        return redirect()->back()->with('success', "{$count} notifications marked as read.");
    }

    /**
     * Remove the specified notification.
     */
    public function destroy(Request $request, Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        $agent = new Agent();
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_notification',
            'module' => 'notification',
            'description' => 'Deleted notification',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }

    /**
     * Clear all notifications.
     */
    public function clearAll(Request $request)
    {
        $user = Auth::user();

        $count = Notification::where('user_id', $user->id)->count();
        Notification::where('user_id', $user->id)->delete();

        $agent = new Agent();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'clear_all',
            'module' => 'notification',
            'description' => "Cleared all {$count} notifications",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
        ]);

        return redirect()->route('notifications.index')
            ->with('success', "All {$count} notifications cleared.");
    }

    /**
     * Get unread notification count (for AJAX).
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications (for dropdown).
     */
    public function getRecent()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'notifications' => $notifications->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'icon' => $notification->icon ?? $this->getIconForType($notification->type),
                    'link' => $notification->link ?? route('notifications.index'),
                    'time' => $notification->created_at->diffForHumans(),
                ];
            }),
            'count' => $notifications->count()
        ]);
    }

    /**
     * Get icon based on notification type.
     */
    private function getIconForType($type)
    {
        $icons = [
            'info' => 'bi-info-circle',
            'success' => 'bi-check-circle',
            'warning' => 'bi-exclamation-triangle',
            'danger' => 'bi-exclamation-circle',
        ];

        return $icons[$type] ?? 'bi-bell';
=======
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        // Mark as read when viewed
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }

    public function clearAll()
    {
        Auth::user()->notifications()->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'All notifications cleared.');
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->notifications()->unread()->count();

        return response()->json([
            'count' => $count,
        ]);
    }

    public function getRecent()
    {
        $notifications = Auth::user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($notifications);
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    }
}