<?php


namespace Meklis\RadiusToNodeny\Nodeny;


use Meklis\RadiusToNodeny\Radius\RadiusInterface;
use Meklis\RadiusToNodeny\Radius\Acct\Request as RadAcct;
use Meklis\RadiusToNodeny\Radius\Auth\Request;
use \Meklis\RadiusToNodeny\Radius\PostAuth\Request as ReqPostAuth;
use Meklis\RadiusToNodeny\Radius\Auth\Response;
use Meklis\RadiusToNodeny\Settings;

class Radius implements RadiusInterface
{
    /**
     * @var Store
     */
    protected $store;
    protected $leaseTimeIp;
    protected $leaseTimePool;

    /**
     * @var Settings
     */
    protected $settings;


    /**
     * Radius constructor.
     * @param Settings $settings
     * @param Store $store
     * @param int $leaseTimeIp
     * @param int $leaseTimePool
     */
    function __construct(Settings $settings, Store $store, $leaseTimeIp = 3600, $leaseTimePool = 120)
    {
        $this->store = $store;
        $this->settings = $settings;
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
     * @param ReqPostAuth $req
     * @return bool
     */
    function radPostAuth(ReqPostAuth $req) {
        $this->store->postAuth(
            $req->getResponse()->getIpAddress(),
            $req->getRequest()->getDeviceMac(),
            $this->settings->get('radius.acct.write_nas') === 'name' ? $req->getRequest()->getNasName() : $req->getRequest()->getNasIp()
        );
        return true;
    }

    /**
     * @param RadAcct $req
     * @return bool
     */
    function radAcct(RadAcct $req)
    {
        $this->store->acct(
          $req->getIpAddress(),
            $req->getDeviceMac(),
            $this->settings->get('radius.acct.write_nas') === 'name' ? $req->getNasName() : $req->getNasIp(),
            $req->getStatusType()
        );
        return true;
    }
}