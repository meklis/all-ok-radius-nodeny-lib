<?php


namespace Meklis\RadiusToNodeny\Radius\RadReply;


class Response
{
        protected $poolName;
        protected $leaseTimeSec;
        protected $ipAddress;
        protected $status;
        protected $error;

        protected function __construct($ip = null, $poolName = null, $leaseTimeSec = 120, $status = "", $error = "")
        {
            $this->ipAddress = $ip;
            $this->poolName = $poolName;
            $this->leaseTimeSec = (int) $leaseTimeSec;
            $this->status = $status;
            $this->error = $error;
        }
        public static function create($ip = null, $poolName = null, $leaseTimeSec=120, $status = "", $error = "") {
            if(!$ip && !$poolName) {
                throw new \InvalidArgumentException("IP or PoolName is required for response");
            }
            return new self($ip, $poolName, $leaseTimeSec, $status, $error);
        }
        public static function createWithoutCheck($ip = null, $poolName = null, $leaseTimeSec=120, $status = "", $error = "") {
            return new self($ip, $poolName, $leaseTimeSec, $status, $error);
        }

        function __toString()
        {
           return json_encode($this->getArray(), JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        }
        function getArray() {
            $resp = [];
            if($this->poolName) {
                $resp['pool_name'] = $this->poolName;
            }
            if($this->ipAddress) {
                $resp['ip_address'] = $this->ipAddress;
            }
            if($this->leaseTimeSec) {
                $resp['lease_time_sec'] = (int) $this->leaseTimeSec;
            }
            if($this->error) {
                $resp['error'] = $this->error;
            }
            if($this->status) {
                $resp['status'] = $this->status;
            }
            return $resp;
        }

    /**
     * @return string
     */
    public function getPoolName()
    {
        if(!$this->poolName) return  "";
        return $this->poolName;
    }

    /**
     * @return int
     */
    public function getLeaseTimeSec(): int
    {
        if(!$this->leaseTimeSec) return  120;
        return $this->leaseTimeSec;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        if(!$this->ipAddress) return  "";
        return $this->ipAddress;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

}