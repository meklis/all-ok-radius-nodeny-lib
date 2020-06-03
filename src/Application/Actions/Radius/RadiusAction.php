<?php


namespace Meklis\RadiusToNodeny\Application\Actions\Radius;


use Meklis\RadiusToNodeny\Application\Actions\Action;
use Meklis\RadiusToNodeny\Radius\RadiusInterface;
use Psr\Log\LoggerInterface;

abstract class RadiusAction extends Action
{
    protected $radius;
    function __construct(LoggerInterface $logger, RadiusInterface $radius)
    {
        $this->radius = $radius;
        parent::__construct($logger);
    }
}

