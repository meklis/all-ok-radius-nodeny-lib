<?php
declare(strict_types=1);

use Meklis\RadiusToNodeny\Application\Middleware\SessionMiddleware;
use Slim\App;

return function (App $app) {
    $app->add(SessionMiddleware::class);
};
