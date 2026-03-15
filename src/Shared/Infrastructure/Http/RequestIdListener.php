<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
class RequestIdListener
{
    #[AsEventListener(event: 'kernel.request')]
    public function onRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $requestId = $request->headers->get('X-Request-Id') ?: $this->generateRequestId();
        $request->attributes->set('request_id', $requestId);
        $request->headers->set('X-Request-Id', $requestId);
    }

    #[AsEventListener(event: 'kernel.response')]
    public function onResponse(ResponseEvent $event): void
    {
        $requestId = $event->getRequest()->attributes->getString('request_id');

        if ($requestId !== '') {
            $event->getResponse()->headers->set('X-Request-Id', $requestId);
        }
    }

    private function generateRequestId(): string
    {
        try {
            return sprintf(
                '%s-%s-%s-%s',
                bin2hex(random_bytes(4)),
                bin2hex(random_bytes(2)),
                bin2hex(random_bytes(2)),
                bin2hex(random_bytes(6))
            );
        } catch (\Throwable) {
            return uniqid('req-', true);
        }
    }
}
