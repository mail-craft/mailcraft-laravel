<?php

declare(strict_types=1);

namespace MailCraft\Laravel;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use MailCraft\MailCraft;

final class MailCraftServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/mailcraft.php', 'mailcraft');

        $this->app->singleton(MailCraft::class, fn () => new MailCraft(
            (string) config('mailcraft.api_key'),
            (string) config('mailcraft.base_url', 'https://api.mailcraft.host/v1'),
        ));
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/mailcraft.php' => config_path('mailcraft.php'),
            ], 'mailcraft-config');
        }

        Mail::extend('mailcraft', fn () => new MailCraftTransport($this->app->make(MailCraft::class)));
    }
}
