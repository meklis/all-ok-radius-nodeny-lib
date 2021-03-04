<?php

namespace Meklis\RadiusToNodeny\Application\Actions\Radius;

use Meklis\RadiusToNodeny\Radius\Auth\Request;
use Psr\Http\Message\ResponseInterface as Response;

class RadAcctAction extends RadiusAction
{
    protected function action(): Response
    {
        $this->logger->debug("Acct: " , $this->getFormData());
        $respond = $this->radius->radAcct(\Meklis\RadiusToNodeny\Radius\Acct\Request::init($this->getFormData()));
        return $this->respondWithData($respond);
    }
}