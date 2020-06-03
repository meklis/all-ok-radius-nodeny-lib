<?php


namespace Meklis\RadiusToNodeny\Nodeny;


use Meklis\RadiusToNodeny\Radius\RadReply\Request;
use Meklis\RadiusToNodeny\Radius\RadReply\Response;

class Radius
{
    /**
     * @var Store
     */
    protected $store;
    protected $leaseTime;

    /**
     * Radius constructor.
     * @param Store $store
     * @param int $leaseTime
     */
    function __construct(Store $store, $leaseTime = 120)
    {
        $this->store = $store;
        $this->leaseTime = $leaseTime;
    }

    /**
     * @param Request $req
     * @return Response
     */
    function radreply(Request $req) {
        $ip =  $this->store->getIp(
            $req->getDeviceMac(),
            $req->getDhcpServerName(),
            "",
            0
        );
        return Response::create($ip, null, $this->leaseTime);
    }
}