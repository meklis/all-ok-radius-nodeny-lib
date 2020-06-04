<?php


namespace Meklis\RadiusToNodeny\Application\Actions\Radius;


use Meklis\RadiusToNodeny\Domain\DomainException\DomainRecordNotFoundException;
use Meklis\RadiusToNodeny\Radius\PostAuth\Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class PostAuthAction extends RadiusAction
{
    protected function action(): Response
    {
        $this->logger->debug("Request: " , $this->getFormData());
        $respond = $this->radius->radPostAuth(Request::init($this->getFormData()));
        return $this->respondWithData($respond);
    }

}