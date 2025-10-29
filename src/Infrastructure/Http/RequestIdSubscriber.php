<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestIdSubscriber implements EventSubscriberInterface
{
    public const HEADER_NAME = 'X-Request-Id';
    public const ATTRIBUTE_KEY = 'request_id';
    private const ATTRIBUTE_START_TIME = '_request_start_time';

    public function __construct(
        #[Autowire(service: 'monolog.logger.request')]
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @return array<string, array{0: string, 1?: int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 2048],
            KernelEvents::RESPONSE => ['onKernelResponse', -2048],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $requestId = $this->resolveRequestId($request->headers->get(self::HEADER_NAME));

        $request->headers->set(self::HEADER_NAME, $requestId);
        $request->attributes->set(self::ATTRIBUTE_KEY, $requestId);
        $request->attributes->set(self::ATTRIBUTE_START_TIME, microtime(true));

        $this->logger->info('Request started', [
            'request_id' => $requestId,
            'method' => $request->getMethod(),
            'uri' => $request->getRequestUri(),
        ]);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $requestId = (string) $request->attributes->get(self::ATTRIBUTE_KEY, '');

        if ($requestId === '') {
            $requestId = $this->resolveRequestId($request->headers->get(self::HEADER_NAME));
            $request->attributes->set(self::ATTRIBUTE_KEY, $requestId);
        }

        $response = $event->getResponse();
        $this->ensureResponseHasRequestId($response, $requestId);

        $duration = null;
        if ($request->attributes->has(self::ATTRIBUTE_START_TIME)) {
            $duration = (microtime(true) - (float) $request->attributes->get(self::ATTRIBUTE_START_TIME)) * 1000;
        }

        $context = [
            'request_id' => $requestId,
            'status_code' => $response->getStatusCode(),
        ];

        if ($duration !== null) {
            $context['duration_ms'] = round($duration, 3);
        }

        $this->logger->info('Request finished', $context);
    }

    private function resolveRequestId(?string $requestId): string
    {
        $requestId = trim((string) $requestId);
        if ($requestId !== '') {
            return $requestId;
        }

        return bin2hex(random_bytes(16));
    }

    private function ensureResponseHasRequestId(Response $response, string $requestId): void
    {
        if ($response->headers->has(self::HEADER_NAME)) {
            return;
        }

        $response->headers->set(self::HEADER_NAME, $requestId);
    }
}

