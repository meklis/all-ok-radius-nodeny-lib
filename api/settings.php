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
    $settings = [
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
            'acct' => [
                'process_start' => $_ENV['PROCESS_ACCT_START'],
                'process_stop' => $_ENV['PROCESS_ACCT_STOP'],
                'write_nas' => isset($_ENV['WRITE_NAS']) ? $_ENV['WRITE_NAS'] : 'ip',
            ]
        ],
    ];

    $containerBuilder->addDefinitions([
        'settings' => $settings,
        \Meklis\RadiusToNodeny\Settings::class => new \Meklis\RadiusToNodeny\Settings($settings),
    ]);
};
