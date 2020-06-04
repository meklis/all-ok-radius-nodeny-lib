<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;


return function (ContainerBuilder $containerBuilder)   {
    // Global Settings Object
    $getLogLevel = function () {
        switch ($_ENV['LOG_LEVEL']) {
            //DEBUG,INFO,NOTICE,WARNING,ERROR,CRITICAL,ALERT,EMERGENCY
            case 'INFO': return Logger::INFO;
            case 'NOTICE': return Logger::NOTICE;
            case 'WARNING': return Logger::WARNING;
            case 'ERROR': return Logger::ALERT;
            case 'EMERGENCY': return Logger::EMERGENCY;
            case 'CRITICAL': return Logger::CRITICAL;
            default:
                return  Logger::DEBUG;
        }
    };

    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => true, // Should be set to false in production
            'logger' => [
                'name' => 'slim-app',
                'path' => isset($_ENV['DOCKER']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => $getLogLevel(),
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
