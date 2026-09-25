<?php

declare(strict_types=1);

namespace MailCraft\Laravel;

use MailCraft\Exceptions\MailCraftApiException;
use MailCraft\MailCraft;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\MessageConverter;

/**
 * Bridges Laravel's Mail facade to the MailCraft API.
 *
 * Registered as the `mailcraft` mailer — set `MAIL_MAILER=mailcraft` and
 * every `Mail::send(...)` / Mailable call routes through MailCraft with no
 * other code changes. Symfony's mailer gives us a fully-formed MIME
 * message; rather than needing a raw-MIME API endpoint, this extracts the
 * structured fields (from/to/subject/html/text/headers) MailCraft's
 * `POST /emails` endpoint expects — the same approach other transactional
 * email providers' Laravel packages use.
 */
final class MailCraftTransport extends AbstractTransport
{
    public function __construct(private readonly MailCraft $mailcraft)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $original = $message->getOriginalMessage();

        if (! $original instanceof Message) {
            throw new TransportException('MailCraft: only Symfony Mime Message instances are supported (received a raw, non-MIME message).');
        }

        $email = MessageConverter::toEmail($original);

        $from = $email->getFrom()[0] ?? null;

        if ($from === null) {
            throw new TransportException('MailCraft: the message has no "from" address.');
        }

        try {
            $this->mailcraft->emails->send(
                from: $from->getAddress(),
                to: $this->addressList($email->getTo()),
                subject: $email->getSubject() ?? '',
                html: $email->getHtmlBody() !== null ? (string) $email->getHtmlBody() : null,
                text: $email->getTextBody() !== null ? (string) $email->getTextBody() : null,
                cc: $this->addressList($email->getCc()),
                bcc: $this->addressList($email->getBcc()),
                replyTo: ($email->getReplyTo()[0] ?? null)?->getAddress(),
                headers: $this->customHeaders($email),
            );
        } catch (MailCraftApiException $exception) {
            throw new TransportException("MailCraft: {$exception->getMessage()}", previous: $exception);
        }
    }

    /**
     * @param  array<int, Address>  $addresses
     * @return array<int, string>
     */
    private function addressList(array $addresses): array
    {
        return array_map(static fn (Address $address) => $address->getAddress(), $addresses);
    }

    /** @return array<string, string> */
    private function customHeaders(Email $email): array
    {
        $headers = [];

        foreach ($email->getHeaders()->all() as $header) {
            if (str_starts_with($header->getName(), 'X-')) {
                $headers[$header->getName()] = $header->getBodyAsString();
            }
        }

        return $headers;
    }

    public function __toString(): string
    {
        return 'mailcraft';
    }
}
