<?php

declare(strict_types=1);

namespace App\Cadastro\UI\Console;

use App\Cadastro\Application\Service\IbgePaisSyncService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:cadastro:sync-paises-ibge', description: 'Sincroniza a tabela de países com a API pública do IBGE.')]
final class SyncPaisesIbgeCommand extends Command
{
    public function __construct(private readonly IbgePaisSyncService $service)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Consultando API de países do IBGE...');
        $result = $this->service->sync();
        $io->success(sprintf('Sincronização concluída. Total: %d, criados: %d, atualizados: %d, inalterados: %d, inativados: %d.', $result['total'], $result['created'], $result['updated'], $result['unchanged'], $result['inactive']));
        return Command::SUCCESS;
    }
}
