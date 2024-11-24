<?php

namespace RiseTechApps\Contact;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use RiseTechApps\Contact\Events\ContactEvent;
use RiseTechApps\Contact\Listeners\ContactListener;

class ContactServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Event::listen(
            ContactEvent::class, ContactListener::class
        );
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Register the main class to use with the facade
        $this->app->singleton('Contact', function () {
            return new Contact();
        });
    }
}
