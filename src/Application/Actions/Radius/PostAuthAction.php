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
        $this->logger->debug("Incomming post-auth request: " , $this->getFormData());
        return $this->respondWithData($this->radius->radPostAuth(Request::init($this->getFormData())));
    }

}