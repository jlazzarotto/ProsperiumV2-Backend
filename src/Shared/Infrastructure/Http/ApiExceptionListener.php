<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\UnauthorizedOperationException;
use App\Shared\Domain\Exception\ValidationException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener(event: 'kernel.exception')]
final class ApiExceptionListener
{
    public function __construct(private readonly JsonResponseFactory $responseFactory)
    {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if ($throwable instanceof ValidationException) {
            $event->setResponse($this->responseFactory->error([
                'message' => $throwable->getMessage(),
                'details' => $throwable->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));

            return;
        }

        if ($throwable instanceof ResourceNotFoundException) {
            $event->setResponse($this->responseFactory->error([
                'message' => $throwable->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND));

            return;
        }

        if ($throwable instanceof UnauthorizedOperationException) {
            $event->setResponse($this->responseFactory->error([
                'message' => $throwable->getMessage(),
            ], JsonResponse::HTTP_FORBIDDEN));
        }
    }
}
