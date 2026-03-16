<?php

declare(strict_types=1);

namespace App\Shared\UI\Console;

use App\Shared\Domain\Contract\TenantDatabaseRegistryInterface;
use DoctrineMigrationsTenant\TenantSchemaV1;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:tenant:provision',
    description: 'Provisiona o schema operacional em um ou mais bancos tenant'
)]
final class TenantProvisionCommand extends Command
{
    public function __construct(
        private readonly TenantDatabaseRegistryInterface $tenantDatabaseRegistry,
        private readonly string $projectDir
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('databaseKey', InputArgument::OPTIONAL, 'Database key do tenant a provisionar (ou --all)')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Provisionar todos os tenants configurados')
            ->addOption('rollback', null, InputOption::VALUE_NONE, 'Reverter o schema (drop tables)')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Exibe as queries sem executar');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $databaseKey = $input->getArgument('databaseKey');
        $all = $input->getOption('all');
        $rollback = $input->getOption('rollback');
        $dryRun = $input->getOption('dry-run');

        if (!$all && ($databaseKey === null || $databaseKey === '')) {
            $io->error('Informe um databaseKey ou use --all para provisionar todos os tenants.');
            return Command::FAILURE;
        }

        $tenants = $all
            ? $this->tenantDatabaseRegistry->listTenants()
            : [['key' => $databaseKey]];

        $schema = new TenantSchemaV1();
        $statements = $rollback ? $schema->down() : $schema->up();
        $action = $rollback ? 'rollback' : 'provision';

        $io->title(sprintf('Tenant %s — schema %s', $action, $schema->getVersion()));

        $hasErrors = false;

        foreach ($tenants as $tenant) {
            $key = $tenant['key'];
            $databaseUrl = $this->tenantDatabaseRegistry->findDatabaseUrl($key);

            if ($databaseUrl === null) {
                $io->warning(sprintf('[%s] Sem database_url configurado — ignorado.', $key));
                continue;
            }

            $io->section(sprintf('[%s] %s', $key, $databaseUrl));

            if ($dryRun) {
                foreach ($statements as $sql) {
                    $io->text($sql . ';');
                }
                $io->success(sprintf('[%s] Dry-run: %d statements.', $key, count($statements)));
                continue;
            }

            try {
                $this->applyStatements($key, $databaseUrl, $statements, $schema, $rollback, $io);
                $io->success(sprintf('[%s] %s concluído — %d statements.', $key, $action, count($statements)));
            } catch (\Throwable $e) {
                $io->error(sprintf('[%s] Erro: %s', $key, $e->getMessage()));
                $hasErrors = true;
            }
        }

        return $hasErrors ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * @param list<string> $statements
     */
    private function applyStatements(
        string $key,
        string $databaseUrl,
        array $statements,
        TenantSchemaV1 $schema,
        bool $rollback,
        SymfonyStyle $io
    ): void {
        $connection = DriverManager::getConnection(['url' => $databaseUrl]);

        try {
            if (!$rollback && $this->isAlreadyApplied($connection, $schema->getVersion())) {
                $io->note(sprintf('[%s] Schema %s já aplicado — nenhuma ação.', $key, $schema->getVersion()));
                return;
            }

            foreach ($statements as $i => $sql) {
                $io->text(sprintf('  [%d/%d] Executando...', $i + 1, count($statements)));
                $connection->executeStatement($sql);
            }

            if (!$rollback) {
                $this->markApplied($connection, $schema);
            }
        } finally {
            $connection->close();
        }
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
