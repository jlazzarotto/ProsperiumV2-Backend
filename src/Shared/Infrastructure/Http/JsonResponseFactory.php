<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

final class JsonResponseFactory
{
    /**
     * @param array<string, mixed> $payload
     */
    public function create(array $payload, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        return new JsonResponse($payload, $status);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function success(array $data, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        return $this->create([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    /**
     * @param array<string, mixed> $error
     */
    public function error(array $error, int $status): JsonResponse
    {
        return $this->create([
            'success' => false,
            'error' => $error,
        ], $status);
    }
}
