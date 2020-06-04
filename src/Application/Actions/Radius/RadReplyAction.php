<?php

namespace Meklis\RadiusToNodeny\Application\Actions\Radius;

use Meklis\RadiusToNodeny\Radius\RadReply\Request;
use Psr\Http\Message\ResponseInterface as Response;

class RadReplyAction extends RadiusAction
{
    protected function action(): Response
    {
        $this->logger->notice("RadReply REQ: " , $this->getFormData());
        $respond = $this->radius->radReply(Request::init($this->getFormData()))->getArray();
        $this->logger->notice("RadReply RESP: " , $respond);
        return $this->respondWithData($respond);
    }
}