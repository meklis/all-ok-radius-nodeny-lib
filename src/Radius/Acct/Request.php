<?php


namespace Meklis\RadiusToNodeny\Radius\Acct;


class Request
{
    protected $nasIp;
    protected $nasName;
    protected $deviceMac;
    protected $dhcpServerName;
    protected $dhcpServerId;
    protected $ipAddress;
    protected $authType;
    protected $classId;
    protected $statusType;
    protected $sessionTime;
    protected $terminateCause;
    protected $inputOctets;
    protected $outputOctets;
    protected $poolName;
    protected $sessionId;

    protected function __construct($fillable)
    {
        if(isset($fillable['nas_ip'])) $this->nasIp = $fillable['nas_ip'];
        if(isset($fillable['nas_name'])) $this->nasName = $fillable['nas_name'];
        if(isset($fillable['device_mac'])) $this->deviceMac = $fillable['device_mac'];
        if(isset($fillable['dhcp_server_name'])) $this->dhcpServerName = $fillable['dhcp_server_name'];
        if(isset($fillable['dhcp_server_id'])) $this->dhcpServerId = $fillable['dhcp_server_id'];
        if(isset($fillable['ip_address'])) $this->ipAddress = $fillable['ip_address'];
        if(isset($fillable['auth_type'])) $this->authType = $fillable['auth_type'];
        if(isset($fillable['class_id'])) $this->classId = $fillable['class_id'];
        if(isset($fillable['status_type'])) $this->statusType = $fillable['status_type'];
        if(isset($fillable['session_time'])) $this->sessionTime = $fillable['session_time'];
        if(isset($fillable['terminate_cause'])) $this->terminateCause = $fillable['terminate_cause'];
        if(isset($fillable['input_octets'])) $this->inputOctets = $fillable['input_octets'];
        if(isset($fillable['output_octets'])) $this->outputOctets = $fillable['output_octets'];
        if(isset($fillable['pool_name'])) $this->poolName = $fillable['pool_name'];
        if(isset($fillable['session_id'])) $this->sessionId = $fillable['session_id'];
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
     * @param mixed $nasIp
     * @return Request
     */
    public function setNasIp($nasIp)
    {
        $this->nasIp = $nasIp;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNasName()
    {
        return $this->nasName;
    }

    /**
     * @param mixed $nasName
     * @return Request
     */
    public function setNasName($nasName)
    {
        $this->nasName = $nasName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeviceMac()
    {
        return $this->deviceMac;
    }

    /**
     * @param mixed $deviceMac
     * @return Request
     */
    public function setDeviceMac($deviceMac)
    {
        $this->deviceMac = $deviceMac;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDhcpServerName()
    {
        return $this->dhcpServerName;
    }

    /**
     * @param mixed $dhcpServerName
     * @return Request
     */
    public function setDhcpServerName($dhcpServerName)
    {
        $this->dhcpServerName = $dhcpServerName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDhcpServerId()
    {
        return $this->dhcpServerId;
    }

    /**
     * @param mixed $dhcpServerId
     * @return Request
     */
    public function setDhcpServerId($dhcpServerId)
    {
        $this->dhcpServerId = $dhcpServerId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param mixed $ipAddress
     * @return Request
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthType()
    {
        return $this->authType;
    }

    /**
     * @param mixed $authType
     * @return Request
     */
    public function setAuthType($authType)
    {
        $this->authType = $authType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClassId()
    {
        return $this->classId;
    }

    /**
     * @param mixed $classId
     * @return Request
     */
    public function setClassId($classId)
    {
        $this->classId = $classId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatusType()
    {
        return $this->statusType;
    }

    /**
     * @param mixed $statusType
     * @return Request
     */
    public function setStatusType($statusType)
    {
        $this->statusType = $statusType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSessionTime()
    {
        return $this->sessionTime;
    }

    /**
     * @param mixed $sessionTime
     * @return Request
     */
    public function setSessionTime($sessionTime)
    {
        $this->sessionTime = $sessionTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTerminateCause()
    {
        return $this->terminateCause;
    }

    /**
     * @param mixed $terminateCause
     * @return Request
     */
    public function setTerminateCause($terminateCause)
    {
        $this->terminateCause = $terminateCause;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInputOctets()
    {
        return $this->inputOctets;
    }

    /**
     * @param mixed $inputOctets
     * @return Request
     */
    public function setInputOctets($inputOctets)
    {
        $this->inputOctets = $inputOctets;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOutputOctets()
    {
        return $this->outputOctets;
    }

    /**
     * @param mixed $outputOctets
     * @return Request
     */
    public function setOutputOctets($outputOctets)
    {
        $this->outputOctets = $outputOctets;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPoolName()
    {
        return $this->poolName;
    }

    /**
     * @param mixed $poolName
     * @return Request
     */
    public function setPoolName($poolName)
    {
        $this->poolName = $poolName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param mixed $sessionId
     * @return Request
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        return $this;
    }


}