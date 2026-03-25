<?php
<<<<<<< HEAD
// app/Providers/EventServiceProvider.php
=======
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
<<<<<<< HEAD
        parent::boot();
=======
        //
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
