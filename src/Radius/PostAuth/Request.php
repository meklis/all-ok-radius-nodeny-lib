<?php


namespace Meklis\RadiusToNodeny\Radius\PostAuth;


use Meklis\RadiusToNodeny\Radius\RadReply\Response;
use Meklis\RadiusToNodeny\Radius\RadReply\Request as RadReq;

class Request
{
    /**
     * @var RadReq
     */
    protected $request;

    /**
     * @return RadReq
     */
    public function getRequest(): RadReq
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
    /**
     * @var Response
     */
    protected $response;
    protected function __construct(RadReq $req, Response $response)
    {
        $this->request = $req;
        $this->response = $response;
    }
    public static function init($data) {
        $req = \Meklis\RadiusToNodeny\Radius\RadReply\Request::init($data['request']);
        $ra = [
          'ip_address' => '',
          'pool_name' => '',
          'lease_time_sec' => 120,
          'status' => '',
          'error' => '',
        ];
        foreach ($ra as $k=>$v) {
            if(isset($data['response'][$k])) {
                $ra[$k] = $data['response'][$k];
            }
        }
        $resp = Response::createWithoutCheck($ra['ip_address'], $ra['pool_name'], $ra['lease_time_sec'], $ra['status'], $ra['error']);
        return new self($req, $resp);
    }
}