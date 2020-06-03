<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Meklis\RadiusToNodeny\Radius\RadiusInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        \Meklis\RadiusToNodeny\Radius\RadiusInterface::class => function(ContainerInterface $c) {
            $conf = $c->get('settings')['radius'];
            $store = new \Meklis\RadiusToNodeny\Nodeny\Store(
                new PDO($conf['database']['dsn'], $conf['database']['username'], $conf['database']['password']),
                $conf['lease_timeouts']['ip']
            );
            return new \Meklis\RadiusToNodeny\Nodeny\Radius($store, $conf['lease_timeouts']['ip'], $conf['lease_timeouts']['pool']);
        }
    ]);

};
