<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Identity\Domain\Entity\User;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Valida que rotas DB_TENANT requerem company_id.
 * ROLE_ROOT sem company_id selecionado recebe erro 400 com mensagem clara.
 */
#[AsEventListener(event: 'kernel.request', priority: 20)]
final class CompanyContextValidationListener
{
    /**
     * Rotas que operam sobre DB_CONTROL e/ou múltiplos tenants agregados
     * Não requerem X-Company-Id header
     * ROLE_ROOT pode acessar livremente
     */
    private const CONTROL_DB_PREFIXES = [
        '/',                              // Home dashboard (dados agregados)
        '/api/v1/me',                     // User info (control DB)
        '/api/v1/companies',              // List/get companies (agregado, sem tenant)
        '/api/v1/empresas',               // List/get empresas (agregado, sem tenant)
        '/api/v1/unidades',               // List/get unidades (agregado, sem tenant)
        '/api/v1/dashboard',              // Dashboard executivo (agregado de todos tenants)
        '/login',                         // Autenticação
        '/definir-senha',                 // Resetar senha
        '/admin/coordenar-empresas',      // Gerenciar companies
        '/admin/provisionar-tenant',      // Provisionar tenant
        '/admin/cadastro-usuarios',       // Gerenciar users
        '/admin/logs',                    // Auditorias
        '/debug',                         // Debug
        '/health',                        // Health check
        '/api/health',                    // API health check
    ];

    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $user = $this->tokenStorage->getToken()?->getUser();

        // Apenas validar se user está autenticado e é ROLE_ROOT
        if (!$user instanceof User || !$user->isRoot()) {
            return;
        }

        // Se é rota de control DB, permitir sem company_id
        if ($this->isControlDbRoute($request->getPathInfo())) {
            return;
        }

        // Se é rota de tenant DB, exigir company_id
        $companyId = $request->headers->get('X-Company-Id');

        if ($companyId === null || trim((string) $companyId) === '') {
            throw new BadRequestHttpException(
                'Você deve selecionar um Grupo Econômico para continuar. '
                . 'Acesse Administrador > Selecionar Grupo Econômico.'
            );
        }
    }

    private function isControlDbRoute(string $path): bool
    {
        foreach (self::CONTROL_DB_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
