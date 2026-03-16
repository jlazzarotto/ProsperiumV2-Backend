<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MultiTenancy;

use App\Company\Domain\Repository\TenantInstanceRepositoryInterface;
use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Repository\UserCompanyRepositoryInterface;
use App\Shared\Domain\Contract\TenantDatabaseRegistryInterface;
use App\Shared\Domain\Exception\UnauthorizedOperationException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsEventListener(event: 'kernel.request')]
final class TenantRequestListener
{
    public function __construct(
        private readonly TenantContext $tenantContext,
        private readonly TenantResolver $tenantResolver,
        private readonly TenantInstanceRepositoryInterface $tenantInstanceRepository,
        private readonly TenantDatabaseRegistryInterface $tenantDatabaseConfigRegistry,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly UserCompanyRepositoryInterface $userCompanyRepository
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $resolvedContext = $this->tenantResolver->resolve($request);

        if ($resolvedContext['companyId'] === null) {
            $resolvedContext['companyId'] = $this->resolveAuthenticatedCompanyId();
        }

        $this->tenantContext->setTenantId($resolvedContext['tenantId']);
        $this->tenantContext->setCompanyId($resolvedContext['companyId']);
        $this->tenantContext->setTenancyMode(null);
        $this->tenantContext->setDatabaseKey(null);
        $this->tenantContext->setResolvedDatabaseUrl(null);
        TenantConnectionRuntime::reset();

        if ($resolvedContext['companyId'] !== null) {
            $this->synchronizeTenantInstanceContext($resolvedContext['companyId'], $resolvedContext['tenantId']);
            $this->guardAuthenticatedUserCompany($resolvedContext['companyId']);
            $this->synchronizeRequestCompanyContext($request, $resolvedContext['companyId']);
        }
    }

    private function guardAuthenticatedUserCompany(int $companyId): void
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User || $user->getId() === null) {
            return;
        }

        // ROLE_ROOT can access any company
        if ($user->isRoot()) {
            return;
        }

        if (!$this->userCompanyRepository->userHasCompany($user->getId(), $companyId)) {
            throw new UnauthorizedOperationException('Usuário autenticado não possui vínculo com a company informada.');
        }
    }

    private function resolveAuthenticatedCompanyId(): ?int
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User || $user->getId() === null) {
            return null;
        }

        $companyIds = $this->userCompanyRepository->listCompanyIdsByUser($user->getId());

        return count($companyIds) === 1 ? $companyIds[0] : null;
    }

    private function synchronizeRequestCompanyContext(Request $request, int $companyId): void
    {
        $queryCompanyId = $request->query->get('companyId');
        if ($queryCompanyId !== null && (int) $queryCompanyId !== $companyId) {
            throw new UnauthorizedOperationException('Company informada na query diverge do contexto autenticado.');
        }

        if ($queryCompanyId === null) {
            $request->query->set('companyId', $companyId);
        }

        $payload = $this->decodeJsonPayload($request);
        if ($payload === null) {
            return;
        }

        if (isset($payload['companyId']) && (int) $payload['companyId'] !== $companyId) {
            throw new UnauthorizedOperationException('Company informada no payload diverge do contexto autenticado.');
        }

        if (!isset($payload['companyId'])) {
            $payload['companyId'] = $companyId;
            $this->replaceRequestContent($request, json_encode($payload, JSON_THROW_ON_ERROR));
        }
    }

    private function synchronizeTenantInstanceContext(int $companyId, ?string $tenantId): void
    {
        $tenantInstance = $this->tenantInstanceRepository->findByCompanyId($companyId);

        if ($tenantInstance === null) {
            return;
        }

        $this->tenantContext->setTenancyMode($tenantInstance->getTenancyMode());
        $this->tenantContext->setDatabaseKey($tenantInstance->getDatabaseKey());
        $this->tenantContext->setTenantId($tenantInstance->getDatabaseKey());

        if ($tenantId !== null && trim($tenantId) !== '' && !ctype_digit(trim($tenantId)) && trim($tenantId) !== $tenantInstance->getDatabaseKey()) {
            throw new UnauthorizedOperationException('Tenant deve ser acessado pelo database_key canônico.');
        }

        $databaseUrl = $this->tenantDatabaseConfigRegistry->findDatabaseUrl($tenantInstance->getDatabaseKey());

        if ($databaseUrl === null) {
            throw new UnauthorizedOperationException('Tenant sem configuração operacional de banco para o database_key informado.');
        }

        $this->tenantContext->setResolvedDatabaseUrl($databaseUrl);
        TenantConnectionRuntime::setDatabaseUrl($databaseUrl);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeJsonPayload(Request $request): ?array
    {
        $contentType = (string) $request->headers->get('Content-Type', '');
        if ($contentType !== '' && !str_contains($contentType, 'json')) {
            return null;
        }

        $content = $request->getContent();
        if ($content === '') {
            return null;
        }

        try {
            $payload = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        return is_array($payload) ? $payload : null;
    }

    private function replaceRequestContent(Request $request, string $content): void
    {
        $reflection = new \ReflectionObject($request);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);
        $property->setValue($request, $content);
    }
}
