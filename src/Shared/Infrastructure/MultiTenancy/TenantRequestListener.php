<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MultiTenancy;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(event: 'kernel.request')]
final class TenantRequestListener
{
    public function __construct(private readonly TenantContext $tenantContext, private readonly TenantResolver $tenantResolver)
    {
    }

    public function __invoke(RequestEvent $event): void
    {
        $this->tenantContext->setTenantId($this->tenantResolver->resolve($event->getRequest()));
    }
}
