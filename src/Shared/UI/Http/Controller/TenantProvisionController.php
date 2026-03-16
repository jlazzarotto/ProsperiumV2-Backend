<?php

declare(strict_types=1);

namespace App\Shared\UI\Http\Controller;

use App\Identity\Domain\Entity\User;
use App\Shared\Domain\Contract\TenantDatabaseRegistryInterface;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use DoctrineMigrationsTenant\TenantSchemaV1;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/admin')]
final class TenantProvisionController extends AbstractController
{
    public function __construct(
        private readonly JsonResponseFactory $responseFactory,
        private readonly TenantDatabaseRegistryInterface $tenantDatabaseRegistry
    ) {
    }

    #[Route('/tenant-provision', name: 'api_v1_admin_tenant_provision', methods: ['POST'])]
    public function provision(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(User::ROLE_ROOT);

        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $databaseKey = isset($payload['databaseKey']) ? trim((string) $payload['databaseKey']) : '';

        if ($databaseKey === '') {
            return $this->responseFactory->error(
                ['message' => 'databaseKey é obrigatório.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $databaseUrl = $this->tenantDatabaseRegistry->findDatabaseUrl($databaseKey);

        if ($databaseUrl === null) {
            return $this->responseFactory->error(
                ['message' => sprintf('databaseKey "%s" não está configurado ou sem database_url em config/tenants.yaml.', $databaseKey)],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $schema = new TenantSchemaV1();
        $statements = $schema->up();
        $executed = 0;
        $errors = [];

        try {
            $connection = DriverManager::getConnection(['url' => $databaseUrl]);
        } catch (\Throwable $e) {
            return $this->responseFactory->error(
                ['message' => sprintf('Não foi possível conectar ao banco tenant: %s', $e->getMessage())],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        try {
            if ($this->isAlreadyApplied($connection, $schema->getVersion())) {
                return $this->responseFactory->success([
                    'status' => 'already_applied',
                    'message' => sprintf('Schema %s já aplicado neste banco tenant.', $schema->getVersion()),
                    'databaseKey' => $databaseKey,
                    'version' => $schema->getVersion(),
                    'statementsTotal' => count($statements),
                    'statementsExecuted' => 0,
                ]);
            }

            foreach ($statements as $sql) {
                try {
                    $connection->executeStatement($sql);
                    $executed++;
                } catch (\Throwable $e) {
                    $errors[] = [
                        'statement' => $executed + 1,
                        'error' => $e->getMessage(),
                    ];
                    break;
                }
            }

            if (count($errors) === 0) {
                $this->markApplied($connection, $schema);
            }
        } finally {
            $connection->close();
        }

        if (count($errors) > 0) {
            return $this->responseFactory->error(
                [
                    'message' => sprintf('Provisionamento falhou no statement %d de %d.', $executed + 1, count($statements)),
                    'status' => 'failure',
                    'databaseKey' => $databaseKey,
                    'version' => $schema->getVersion(),
                    'statementsTotal' => count($statements),
                    'statementsExecuted' => $executed,
                    'errors' => $errors,
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->responseFactory->success([
            'status' => 'success',
            'message' => sprintf('Schema %s provisionado com sucesso.', $schema->getVersion()),
            'databaseKey' => $databaseKey,
            'version' => $schema->getVersion(),
            'statementsTotal' => count($statements),
            'statementsExecuted' => $executed,
        ]);
    }

    private function isAlreadyApplied(Connection $connection, string $version): bool
    {
        try {
            $result = $connection->fetchOne(
                'SELECT version FROM tenant_schema_versions WHERE version = ?',
                [$version]
            );

            return $result !== false;
        } catch (\Throwable) {
            return false;
        }
    }

    private function markApplied(Connection $connection, TenantSchemaV1 $schema): void
    {
        $connection->executeStatement(
            'INSERT INTO tenant_schema_versions (version, description, applied_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE applied_at = VALUES(applied_at)',
            [
                $schema->getVersion(),
                $schema->getDescription(),
                (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]
        );
    }
}
