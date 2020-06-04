<?php


namespace Meklis\RadiusToNodeny\Nodeny;


use Meklis\RadiusToNodeny\Radius\RadiusInterface;
use Meklis\RadiusToNodeny\Radius\RadReply\Request;
use \Meklis\RadiusToNodeny\Radius\PostAuth\Request as ReqPostAuth;
use Meklis\RadiusToNodeny\Radius\RadReply\Response;

class Radius implements RadiusInterface
{
    /**
     * @var Store
     */
    protected $store;
    protected $leaseTimeIp;
    protected $leaseTimePool;



    /**
     * Radius constructor.
     * @param Store $store
     * @param int $leaseTimeIp
     * @param int $leaseTimePool
     */
    function __construct(Store $store, $leaseTimeIp = 3600, $leaseTimePool = 120)
    {
        $this->store = $store;
        $this->leaseTimeIp = $leaseTimeIp;
        $this->leaseTimePool = $leaseTimePool;
    }

    /**
     * @return Store
     */
    function getStore() {
        return $this->store;
    }
    /**
     * @TODO parsing of option82
     * @param Request $req
     * @return Response
     */
    function radReply(Request $req): Response {
        $ip =  $this->store->getIp(
            $req->getDeviceMac(),
            $req->getDhcpServerName(),
            "",
            0
        );
        if(!$ip) {
            return  Response::create(null, $req->getDhcpServerName(), $this->leaseTimePool);
        }
        return Response::create($ip, null, $this->leaseTimeIp);
    }

    /**
     * @TODO Implement postauth
     * @param ReqPostAuth $req
     * @return bool
     */
    function radPostAuth(ReqPostAuth $req) {
        $this->store->postAuth(
            $req->getResponse()->getIpAddress(),
            $req->getRequest()->getDeviceMac(),
            $req->getRequest()->getNasName()
        );
        return true;
    }
}