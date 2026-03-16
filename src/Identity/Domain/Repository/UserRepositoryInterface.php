<?php

declare(strict_types=1);

namespace App\Identity\Domain\Repository;

use App\Identity\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function findByEmailAndCompany(string $email, int $companyId): ?User;

    public function countAll(): int;

    /**
     * @return list<User>
     */
    public function listAll(?int $companyId = null, ?string $status = null): array;
}
