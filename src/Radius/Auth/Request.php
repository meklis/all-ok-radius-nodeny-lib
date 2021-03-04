<?php


namespace Meklis\RadiusToNodeny\Radius\Auth;


class Request
{
    protected $nasIp;
    protected $nasName;
    protected $deviceMac;
    protected $dhcpServerName;
    protected $agentRemoteId;
    protected $agentCircuitId;

    protected function __construct($fillable)
    {
        $this->nasIp = $fillable['nas_ip'];
        $this->nasName = $fillable['nas_name'];
        $this->deviceMac = $fillable['device_mac'];
        $this->dhcpServerName = $fillable['dhcp_server_name'];
        if(isset($fillable['agent'])) {
            $this->agentRemoteId = isset($fillable['agent']['remote_id']) ? $fillable['agent']['remote_id'] : null;
            $this->agentCircuitId = isset($fillable['agent']['_raw_circuit_id']) ? $fillable['agent']['_raw_circuit_id'] : null;
        }
    }
    public static function init($data) {
        return new self($data);
    }

    /**
     * @return mixed
     */
    public function getNasIp()
    {
        return $this->nasIp;
    }

    /**
     * @return mixed
     */
    public function getNasName()
    {
        return $this->nasName;
    }

    /**
     * @return mixed
     */
    public function getDeviceMac()
    {
        return $this->deviceMac;
    }

    /**
     * @return mixed
     */
    public function getDhcpServerName()
    {
        return $this->dhcpServerName;
    }

    /**
     * @return mixed|null
     */
    public function getAgentRemoteId()
    {
        return $this->agentRemoteId;
    }

    /**
     * @return mixed|null
     */
    public function getAgentCircuitId()
    {
        return $this->agentCircuitId;
    }
}