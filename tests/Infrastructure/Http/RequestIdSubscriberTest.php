<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Http;

use App\Infrastructure\Http\RequestIdSubscriber;
use Monolog\Handler\TestHandler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RequestIdSubscriberTest extends WebTestCase
{
    public function testItGeneratesRequestIdAndLogsLifecycle(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/scores', ['name' => 'Dana', 'reactionTime' => 110]);

        $response = $client->getResponse();
        self::assertTrue($response->headers->has(RequestIdSubscriber::HEADER_NAME));
        $requestId = $response->headers->get(RequestIdSubscriber::HEADER_NAME);
        self::assertIsString($requestId);
        self::assertNotSame('', $requestId);

        /** @var TestHandler $handler */
        $handler = static::getContainer()->get('monolog.handler.request');
        self::assertInstanceOf(TestHandler::class, $handler);

        $records = $handler->getRecords();
        self::assertCount(2, $records);
        self::assertSame('Request started', $records[0]['message']);
        self::assertSame('Request finished', $records[1]['message']);

        self::assertSame($requestId, $records[0]['context']['request_id'] ?? null);
        self::assertSame($requestId, $records[1]['context']['request_id'] ?? null);
        self::assertSame('POST', $records[0]['context']['method'] ?? null);
        self::assertSame(201, $records[1]['context']['status_code'] ?? null);
        self::assertArrayHasKey('duration_ms', $records[1]['context']);
    }

    public function testItUsesProvidedRequestId(): void
    {
        $client = static::createClient();
        $providedId = 'req-123';
        $client->setServerParameters([
            'HTTP_' . strtoupper(str_replace('-', '_', RequestIdSubscriber::HEADER_NAME)) => $providedId,
        ]);

        $client->jsonRequest('POST', '/api/scores', ['name' => 'Eve', 'reactionTime' => 130]);

        $response = $client->getResponse();
        self::assertSame($providedId, $response->headers->get(RequestIdSubscriber::HEADER_NAME));

        /** @var TestHandler $handler */
        $handler = static::getContainer()->get('monolog.handler.request');
        $lastRecord = $handler->getRecords()[1] ?? null;
        self::assertIsArray($lastRecord);
        self::assertSame($providedId, $lastRecord['context']['request_id'] ?? null);
    }
}
