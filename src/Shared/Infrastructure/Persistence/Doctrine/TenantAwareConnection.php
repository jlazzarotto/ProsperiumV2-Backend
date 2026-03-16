<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Infrastructure\MultiTenancy\TenantConnectionRuntime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\DsnParser;

final class TenantAwareConnection extends Connection
{
    public function connect()
    {
        $this->synchronizeRuntimeParameters();

        return parent::connect();
    }

    private function synchronizeRuntimeParameters(): void
    {
        $runtimeUrl = TenantConnectionRuntime::getDatabaseUrl();

        if ($runtimeUrl === null || $runtimeUrl === '') {
            return;
        }

        $params = $this->getParams();
        $currentUrl = isset($params['url']) && is_string($params['url']) ? trim($params['url']) : null;

        if ($currentUrl === $runtimeUrl) {
            return;
        }

        $parsedParams = (new DsnParser())->parse($runtimeUrl);
        $mergedParams = array_replace($params, $parsedParams, ['url' => $runtimeUrl]);

        $reflection = new \ReflectionObject($this);
        $property = $reflection->getParentClass()?->getProperty('params');

        if ($property === null) {
            return;
        }

        $property->setAccessible(true);
        $property->setValue($this, $mergedParams);
    }
}
