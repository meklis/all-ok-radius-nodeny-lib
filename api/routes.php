<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Nodeny all-ok-radius API');
        return $response;
    });
    $app->group('/v1', function (Group $group) {
            $group->post('/radius-request', \Meklis\RadiusToNodeny\Application\Actions\Radius\RadReplyAction::class);
            $group->post('/radius-post-auth', \Meklis\RadiusToNodeny\Application\Actions\Radius\PostAuthAction::class);
            $group->post('/radius-acct', \Meklis\RadiusToNodeny\Application\Actions\Radius\RadAcctAction::class);
    });

};
