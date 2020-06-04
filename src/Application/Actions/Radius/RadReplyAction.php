<?php

namespace Meklis\RadiusToNodeny\Application\Actions\Radius;

use Meklis\RadiusToNodeny\Radius\RadReply\Request;
use Psr\Http\Message\ResponseInterface as Response;

class RadReplyAction extends RadiusAction
{
    protected function action(): Response
    {
        $this->logger->debug("Incomming radreply request: " , $this->getFormData());
        return $this->respondWithData($this->radius->radReply(Request::init($this->getFormData()))->getArray());
    }
}