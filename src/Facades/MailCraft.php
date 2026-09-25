<?php

declare(strict_types=1);

namespace MailCraft\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use MailCraft\Resources\Campaigns;
use MailCraft\Resources\Contacts;
use MailCraft\Resources\Domains;
use MailCraft\Resources\Emails;
use MailCraft\Resources\Lists;
use MailCraft\Resources\Metrics;
use MailCraft\Resources\Properties;
use MailCraft\Resources\Segments;
use MailCraft\Resources\Senders;
use MailCraft\Resources\Suppressions;
use MailCraft\Resources\TemplateFolders;
use MailCraft\Resources\Templates;
use MailCraft\Resources\Webhooks;
use MailCraft\MailCraft as MailCraftClient;

/**
 * @method static Emails emails()
 * @method static Domains domains()
 * @method static Senders senders()
 * @method static Contacts contacts()
 * @method static Lists lists()
 * @method static Segments segments()
 * @method static Properties properties()
 * @method static Templates templates()
 * @method static TemplateFolders templateFolders()
 * @method static Campaigns campaigns()
 * @method static Webhooks webhooks()
 * @method static Suppressions suppressions()
 * @method static Metrics metrics()
 *
 * @see MailCraftClient
 */
final class MailCraft extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MailCraftClient::class;
    }
}
