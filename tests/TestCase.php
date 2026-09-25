<?php

declare(strict_types=1);

namespace MailCraft\Laravel\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use MailCraft\Laravel\MailCraftServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [MailCraftServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('mailcraft.api_key', 'sk_test_key');
        $app['config']->set('mail.default', 'mailcraft');
        $app['config']->set('mail.mailers.mailcraft', ['transport' => 'mailcraft']);
    }
}
