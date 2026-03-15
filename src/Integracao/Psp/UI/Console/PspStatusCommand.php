<?php

declare(strict_types=1);

namespace App\Integracao\Psp\UI\Console;

use App\Integracao\Psp\Application\Service\PspDuvService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:integracao:psp:status', description: 'Testa autenticação com a API do Porto sem Papel.')]
final class PspStatusCommand extends Command
{
    public function __construct(private readonly PspDuvService $service)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Testando autenticação PSP...');

        $result = $this->service->testAuthentication();

        $io->success(sprintf('Autenticação OK. Token: %s', $result['tokenPreview']));

        return self::SUCCESS;
    }
}
