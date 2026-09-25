<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Mail;
use MailCraft\Client;
use MailCraft\MailCraft;

test('sending mail through the mailcraft mailer calls the mailcraft api with the right payload', function () {
    $mockHandler = new MockHandler([
        new Response(201, ['Content-Type' => 'application/json'], json_encode([
            'data' => ['id' => 'em_123', 'status' => 'queued'],
        ])),
    ]);

    $requestHistory = [];
    $handlerStack = HandlerStack::create($mockHandler);
    $handlerStack->push(Middleware::history($requestHistory));

    $guzzle = new GuzzleClient([
        'base_uri' => 'https://api.mailcraft.host/v1/',
        'handler' => $handlerStack,
        'headers' => ['Authorization' => 'Bearer sk_test_key'],
    ]);
    $client = new Client('sk_test_key', httpClient: $guzzle);
    $mailcraft = new MailCraft('sk_test_key', client: $client);

    $this->app->instance(MailCraft::class, $mailcraft);

    Mail::mailer('mailcraft')->html('<p>Thanks for signing up.</p>', function ($message) {
        $message->from('hello@example.com')
            ->to('person@example.com')
            ->subject('Welcome!');
    });

    expect($requestHistory)->toHaveCount(1);

    $request = $requestHistory[0]['request'];
    expect($request->getMethod())->toBe('POST');
    expect((string) $request->getUri())->toBe('https://api.mailcraft.host/v1/emails');
    expect($request->getHeaderLine('Authorization'))->toBe('Bearer sk_test_key');

    $body = json_decode((string) $request->getBody(), true);
    expect($body['from'])->toBe('hello@example.com');
    expect($body['to'])->toBe(['person@example.com']);
    expect($body['subject'])->toBe('Welcome!');
    expect($body['html'])->toContain('Thanks for signing up');
});
