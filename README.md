# mail-craft/mailcraft-laravel

Official Laravel integration for [MailCraft](https://mailcraft.host) — wraps [`mail-craft/mailcraft-php`](https://github.com/mail-craft/mailcraft-php) with a Mail transport, a facade, and config publishing.

## Install

```bash
composer require mail-craft/mailcraft-laravel
```

Publish the config (optional — it works from env vars alone):

```bash
php artisan vendor:publish --tag=mailcraft-config
```

```env
MAILCRAFT_API_KEY=sk_live_...
MAIL_MAILER=mailcraft
```

## Usage

### As a Mail transport (zero code changes)

With `MAIL_MAILER=mailcraft` set, every existing `Mail::send(...)` / Mailable / notification call routes through MailCraft automatically:

```php
Mail::to('person@example.com')->send(new WelcomeEmail($user));
```

The package converts the Symfony MIME message Laravel builds into the structured fields MailCraft's API expects (from/to/subject/html/text/headers) — no raw-MIME endpoint required.

### Using the client/facade directly

For anything beyond sending mail — contacts, campaigns, templates, domains, etc. — use the facade or inject the client:

```php
use MailCraft\Laravel\Facades\MailCraft;

MailCraft::contacts()->upsert(email: 'person@example.com', firstName: 'Ada');
MailCraft::campaigns()->send($campaignId);
```

```php
use MailCraft\MailCraft;

class SubscribeController
{
    public function __invoke(MailCraft $mailcraft)
    {
        $mailcraft->contacts->upsert(email: request('email'));
    }
}
```

Both styles reach the same underlying resources — see [`mail-craft/mailcraft-php`'s README](https://github.com/mail-craft/mailcraft-php) for the full resource list.

## Requirements

PHP 8.1+, Laravel 10, 11, 12, or 13.

## License

MIT
