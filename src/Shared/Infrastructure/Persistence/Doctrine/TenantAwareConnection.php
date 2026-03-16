<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Infrastructure\MultiTenancy\TenantConnectionRuntime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\DsnParser;

final class TenantAwareConnection extends Connection
{
    private ?string $currentRuntimeUrl = null;

    public function connect(): bool
    {
        $urlChanged = $this->synchronizeRuntimeParameters();

        if ($urlChanged && $this->isConnected()) {
            $this->close();
        }

        return parent::connect();
    }

    /**
     * @return bool true se a URL mudou e precisa reconectar
     */
    private function synchronizeRuntimeParameters(): bool
    {
        $runtimeUrl = TenantConnectionRuntime::getDatabaseUrl();

        if ($runtimeUrl === null || $runtimeUrl === '') {
            return false;
        }

        if ($this->currentRuntimeUrl === $runtimeUrl) {
            return false;
        }

        $params = $this->getParams();
        $parsedParams = (new DsnParser())->parse($runtimeUrl);
        $mergedParams = array_replace($params, $parsedParams, ['url' => $runtimeUrl]);

        $reflection = new \ReflectionObject($this);
        $property = $reflection->getParentClass()?->getProperty('params');

        if ($property === null) {
            return false;
        }

        $property->setAccessible(true);
        $property->setValue($this, $mergedParams);

        $previousUrl = $this->currentRuntimeUrl;
        $this->currentRuntimeUrl = $runtimeUrl;

        return $previousUrl !== null;
    }
}
