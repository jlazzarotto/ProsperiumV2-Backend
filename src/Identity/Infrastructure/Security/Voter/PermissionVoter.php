<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Security\Voter;

use App\Identity\Application\Security\PermissionContext;
use App\Identity\Application\Service\AuthorizationService;
use App\Identity\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

final class PermissionVoter extends Voter
{
    public const ATTRIBUTE = 'IDENTITY_PERMISSION';

    public function __construct(private readonly AuthorizationService $authorizationService)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::ATTRIBUTE && $subject instanceof PermissionContext;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $this->authorizationService->can($user, $subject);
    }
}
