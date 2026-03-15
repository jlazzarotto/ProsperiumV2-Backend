<?php

declare(strict_types=1);

namespace App\Cadastro\UI\Console;

use App\Cadastro\Application\Service\IbgeUfSyncService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:cadastro:sync-ufs-ibge', description: 'Sincroniza a tabela de UFs com a API pública do IBGE.')]
final class SyncUfsIbgeCommand extends Command
{
    public function __construct(private readonly IbgeUfSyncService $service)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Consultando API de UFs do IBGE...');
        $result = $this->service->sync();
        $io->success(sprintf('Sincronização concluída. Total: %d, criados: %d, atualizados: %d, inalterados: %d, inativados: %d.', $result['total'], $result['created'], $result['updated'], $result['unchanged'], $result['inactive']));
        return Command::SUCCESS;
    }
}
