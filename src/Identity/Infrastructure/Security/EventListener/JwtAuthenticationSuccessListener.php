<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Security\EventListener;

use App\Identity\Application\Service\UserService;
use App\Identity\Domain\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

final class JwtAuthenticationSuccessListener
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $this->userService->registerSuccessfulLogin($user);
    }
}
