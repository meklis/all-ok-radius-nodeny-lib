<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => true, // Should be set to false in production
            'logger' => [
                'name' => 'slim-app',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'radius' => [
                'database' => [
                    'dsn' => $_ENV['DATABASE_DSN'],
                    'username' => $_ENV['DATABASE_USERNAME'],
                    'password' => $_ENV['DATABASE_PASSWORD'],
                ],
                'lease_timeouts' => [
                    'ip' => $_ENV['LEASETIME_IP'],
                    'pool' => $_ENV['LEASETIME_POOL'],
                ],
            ],
        ],
    ]);
};
