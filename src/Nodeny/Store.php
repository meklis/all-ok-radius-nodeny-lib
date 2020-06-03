<?php


namespace Meklis\RadiusToNodeny\Nodeny;


use Meklis\RadiusToNodeny\Helpers;

class Store
{
    protected $conn;
    protected $leaseTime;
    function __construct(\PDO $conn, $leaseTime = 120)
    {
        $this->leaseTime = $leaseTime;
        $this->conn = $conn;
    }
    public function getIp($mac_address, $dhcp_server_name = "", $device_mac = "", $device_port = 0) :?string {
        //Проверка полного совпадения
        $registered = $this->findRegisteredIp($mac_address, $dhcp_server_name, $device_mac, $device_port, "");
        if($registered) {
            $this->updateBinding($registered['user_id'], $registered['ip_id'], $registered['mac_id']);
            return $registered['ip'];
        }
        $macInfo = $this->findRegisteredMac($mac_address, $dhcp_server_name, $device_mac, $device_port);
        if($macInfo) {
            $this->clearOldIpsByMac($mac_address, $dhcp_server_name);
            $ipData = $this->findFreeIp($dhcp_server_name, $macInfo['real_ip']);
            $this->updateBinding($macInfo['user_id'],$ipData['ip_id'],$macInfo['mac_id'] );
            return $ipData['ip'];
        }
        return null;
    }
    function findFreeIp($tag, $isReal = false) {
        $isReal = $isReal ? 1 : 0;
        $tag = "%,{$tag},%";
        $sth = $this->conn->prepare("
                SELECT  id ip_id, INET_NTOA(ip) ip 
                FROM ip_pool 
                WHERE type = 'dynamic'  
                and `release` < UNIX_TIMESTAMP() 
                and realip = ? 
                and tags like ? 
                and uid = 0 
                ORDER BY 1 LIMIT 1");
        $sth->execute([$isReal, $tag]);
        if($sth->rowCount() == 0) {
            throw new \Exception("Not found free IP address in ip_pool");
        }
        $data = $sth->fetch();
        return $data;
    }
    function updateBinding($user_id, $ip_id, $mac_id) {
        $this->conn->prepare("
            UPDATE ip_pool SET uid = ?, `release` = UNIX_TIMESTAMP() + {$this->leaseTime}
            WHERE id = ?;
        ")->execute([$user_id, $ip_id]);
        $this->conn->prepare("
            UPDATE mac_uid SET ip = (SELECT ip FROM ip_pool WHERE id = ? LIMIT 1), uid = ?, time=UNIX_TIMESTAMP() WHERE id = ?;
        ")->execute([$ip_id, $user_id, $mac_id]);
    }
    function findRegisteredIp($mac_address, $tag = "", $device_mac = "", $device_port = 0, $type = "static") {
        $arguments = [Helpers::prepareMac($mac_address)];
        $prepared_query = "
            SELECT 
            m.id mac_id , p.id `ip_id`, INET_NTOA(p.ip) ip, p.type, p.uid user_id 
            FROM mac_uid m
            JOIN ip_pool p on p.uid = m.uid
            WHERE m.uid != 0 and m.mac = ? 
        ";
        if($type) {
            $arguments[] = $type;
            $prepared_query .= " p.type = ? ";
        }
        if($tag) {
            $prepared_query .= " and tags like ?";
            $arguments[] = "%,{$tag},%";
        }
        if($device_mac && $device_port) {
            $prepared_query .= " and m.device_mac = ? and m.device_port = ?  ";
            $arguments[] = Helpers::prepareMac($device_mac);
            $arguments[] = $device_port;
        }
        $sth = $this->conn->prepare($prepared_query);
        $sth->execute($arguments);
        if($sth->rowCount() < 1) {
            return null;
        }
        return  $sth->fetch();
    }

    function findRegisteredMac($mac_address, $tag = "", $device_mac = "", $device_port = "") {
        $arguments = [$mac_address, "%$tag%"];
        $query = "SELECT m.id mac_id, m.uid user_id , if(r.uid is null, 0, 1 ) real_ip 
                    FROM data0 d
                    JOIN mac_uid m on m.uid = d.uid 
                    LEFT JOIN (SELECT uid FROM users_services WHERE tags LIKE '%,realip,%') r on r.uid = m.uid 
                    WHERE m.mac = ?  and _ip_tag like ?";
        if($device_mac) {
            $arguments[] = $device_mac;
            $query .= " and m.device_mac = ?";
        }
        if($device_mac) {
            $arguments[] = $device_port;
            $query .= " and m.device_port = ?";
        }
        $sth = $this->conn->prepare($query . " LIMIT 1 ");
        $sth->execute($arguments);
        if($sth->rowCount() == 0) {
            return null;
        }
        return  $sth->fetch();
    }

    function clearOldDynamicIps($time = 3600) {
        $this->conn->exec("UPDATE ip_pool SET uid = 0, release = 0 WHERE uid=0 AND release < (UNIX_TIMESTAMP()-{$time});");
        return $this;
    }
    function clearOldIpsByMac($mac, $dhcpServerName) {
        $this->conn->prepare("
            UPDATE ip_pool SET uid = 0, release = 0 
            WHERE ip in (SELECT ip FROM mac_uid WHERE mac = ?) and tags like ?")->execute([$mac, "%,{$dhcpServerName},%"]);
        return $this;
    }
    function postAuth($usrIp, $macAddr, $nasName) {
        $properties = "mod=dhcp;user=" . Helpers::prepareMac($macAddr) . ";nas=$nasName";
        $this->conn->prepare("INSERT INTO auth_now SET
        ip = ?,
        properties = ?,
        start = UNIX_TIMESTAMP(),
        last = UNIX_TIMESTAMP()
    ON DUPLICATE KEY UPDATE
        properties = ?,
        last = UNIX_TIMESTAMP();")->execute([$usrIp, $properties, $properties]);
    }
}