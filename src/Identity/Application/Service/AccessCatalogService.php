<?php

declare(strict_types=1);

namespace App\Identity\Application\Service;

use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Repository\ModuloRepositoryInterface;
use App\Identity\Domain\Repository\UserPerfilRepositoryInterface;

final class AccessCatalogService
{
    public function __construct(
        private readonly ModuloRepositoryInterface $moduloRepository,
        private readonly UserPerfilRepositoryInterface $userPerfilRepository
    ) {
    }

    /**
     * @return array<string, bool>
     */
    public function buildEnabledModules(): array
    {
        $result = [];

        foreach ($this->moduloRepository->listAllActive() as $modulo) {
            $result[$modulo->getCodigo()] = true;
        }

        return $result;
    }

    /**
     * @return array<string, array{ver: bool, criar_editar: bool, deletar: bool}>
     */
    public function buildPermissionMatrix(User $user): array
    {
        $modules = $this->moduloRepository->listAllActive();
        $result = [];

        if ($user->isRoot()) {
            foreach ($modules as $modulo) {
                $result[$modulo->getCodigo()] = [
                    'ver' => true,
                    'criar_editar' => true,
                    'deletar' => true,
                ];
            }

            return $result;
        }

        $permissionCodes = $this->userPerfilRepository->listPermissionCodesByUser((int) $user->getId());
        $permissionSet = array_fill_keys($permissionCodes, true);

        foreach ($modules as $modulo) {
            $prefix = $modulo->getCodigo();
            $canDelete = isset($permissionSet[$prefix . '.delete']);
            $canManage = isset($permissionSet[$prefix . '.create_edit']) || $canDelete;
            $canView = isset($permissionSet[$prefix . '.view']) || $canManage;

            $result[$prefix] = [
                'ver' => $canView,
                'criar_editar' => $canManage,
                'deletar' => $canDelete,
            ];
        }

        return $result;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function buildMenu(User $user): array
    {
        $matrix = $this->buildPermissionMatrix($user);
        $categories = [];

        foreach ($this->moduloRepository->listMenuEntries() as $modulo) {
            $canView = $matrix[$modulo->getCodigo()]['ver'] ?? false;
            if (!$canView) {
                continue;
            }

            $categoryCode = $modulo->getCategoriaCodigo();
            if (!isset($categories[$categoryCode])) {
                $categories[$categoryCode] = [
                    'code' => $categoryCode,
                    'label' => $modulo->getCategoriaNome(),
                    'items' => [],
                ];
            }

            $categories[$categoryCode]['items'][] = [
                'label' => $modulo->getMenuLabel() ?? $modulo->getNome(),
                'href' => $modulo->getRoutePath(),
                'iconKey' => $modulo->getIconKey(),
                'permissionKey' => $modulo->getCodigo(),
            ];
        }

        return array_values($categories);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listModulesCatalog(): array
    {
        $items = [];

        foreach ($this->moduloRepository->listAllActive() as $modulo) {
            $items[] = [
                'key' => $modulo->getCodigo(),
                'label' => $modulo->getNome(),
                'category' => $modulo->getCategoriaCodigo(),
                'categoryLabel' => $modulo->getCategoriaNome(),
                'menuLabel' => $modulo->getMenuLabel(),
                'routePath' => $modulo->getRoutePath(),
                'iconKey' => $modulo->getIconKey(),
                'sortOrder' => $modulo->getSortOrder(),
                'isMenuEntry' => $modulo->isMenuEntry(),
            ];
        }

        return $items;
    }
}
