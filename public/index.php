<?php

declare(strict_types=1);

use App\Kernel;

if (!is_file(dirname(__DIR__) . '/vendor/autoload.php')) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Dependencies not installed.']);
    exit;
}

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return static function (array $context): Kernel {
    require_once dirname(__DIR__) . '/config/bootstrap.php';

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
