<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\UserNotification;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /**
         * Share current logged-in user
         */
        View::composer('*', function ($view) {
            $view->with('currentUser', auth()->user());
        });

        /**
         * Share notifications data with main layout
         */
        View::composer('layouts.app', function ($view) {

            if (Auth::check()) {
                $notifications = UserNotification::where('user_id', Auth::id())
                    ->latest()
                    ->limit(5)
                    ->get();

                $unreadCount = $notifications->where('is_read', false)->count();
            } else {
                $notifications = collect();
                $unreadCount = 0;
            }

            $view->with([
                'notifications' => $notifications,
                'unreadNotificationsCount' => $unreadCount,
            ]);
        });
    }
}
