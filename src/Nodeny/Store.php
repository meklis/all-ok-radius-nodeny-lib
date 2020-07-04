<?php


namespace Meklis\RadiusToNodeny\Nodeny;


use Meklis\RadiusToNodeny\Helpers;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class Store
{
    protected $conn;
    protected $leaseTime;
    protected $logger;
    function __construct(\PDO $conn, $leaseTime = 120)
    {
        $this->leaseTime = $leaseTime;
        $this->conn = $conn;
        $this->logger = new Logger('null', [new NullHandler(Logger::CRITICAL)]);
    }
    function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    public function getIp($mac_address, $dhcp_server_name = "", $device_mac = "", $device_port = 0) :?string {
        //Проверка полного совпадения
        $registered = $this->findRegisteredIp($mac_address, $dhcp_server_name, $device_mac, $device_port, "");
        if($registered) {
            $this->logger->debug("Registered IP found - {$registered['ip']}", ['mac'=>$mac_address,'tag'=>$dhcp_server_name,'sw_mac'=>$device_mac,'sw_port' => $device_port]);
            $this->updateBinding($registered['user_id'], $registered['ip_id'], $registered['mac_id']);
            return $registered['ip'];
        }
        $this->logger->notice("Registered ip not found ", [$mac_address, $dhcp_server_name]);
        $macInfo = $this->findRegisteredMac($mac_address, $dhcp_server_name, $device_mac, $device_port);
        if($macInfo) {
            $this->logger->debug("findRegisteredMac - user={$macInfo['user_id']}", [$mac_address, $dhcp_server_name]);
            $this->clearOldIpsByMac($mac_address, $dhcp_server_name);
            try {
                $ipData = $this->findFreeIp($dhcp_server_name, $macInfo['real_ip']);
                $this->logger->debug("Found free IP address, ip={$ipData['ip']}", [$dhcp_server_name, $macInfo['real_ip']]);
                $this->updateBinding($macInfo['user_id'], $ipData['ip_id'], $macInfo['mac_id']);
                return $ipData['ip'];
            } catch (\Exception $e) {
                $this->logger->notice("findRegisteredMac: {$e->getMessage()}", [$dhcp_server_name, $macInfo['real_ip']]);
                return  null;
            }
        } else {
            $this->logger->notice("findRegisteredMac - not found", [$mac_address, $dhcp_server_name, $device_mac, $device_port]);
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
        $this->logger->notice("Update release time for user $user_id with ip-id $ip_id");
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
        $mac_address = Helpers::prepareMac($mac_address);
        $device_mac = Helpers::prepareMac($device_mac);
        $this->logger->debug("Try find MAC address in mac_uid-data0", [$mac_address, $tag, $device_mac, $device_port]);
        $query = "SELECT m.id mac_id, m.uid user_id , if(r.uid is null, 0, 1 ) real_ip 
                    FROM data0 d
                    JOIN mac_uid m on m.uid = d.uid 
                    LEFT JOIN (SELECT uid FROM users_services WHERE tags LIKE '%,realip,%') r on r.uid = m.uid 
                    WHERE m.mac = '$mac_address'  and _ip_tag like '%{$tag}%'";
        if($device_mac) {
            $query .= " and m.device_mac = '$device_mac'";
        }
        if($device_port) {
            $query .= " and m.device_port = '$device_port'";
        }
        $sth = $this->conn->query($query);
        $data = $sth->fetchAll();
        if(count($data) == 0) {
            return  null;
        }
        return  $data[0];
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
        if(!$usrIp || !$macAddr) return false;
        $properties = "mod=dhcp;user=" . Helpers::prepareMac($macAddr) . ";nas=$nasName";
        return $this->conn->prepare("INSERT INTO auth_now SET
        ip = ?,
        properties = ?,
        start = UNIX_TIMESTAMP(),
        last = UNIX_TIMESTAMP()
    ON DUPLICATE KEY UPDATE
        properties = ?,
        last = UNIX_TIMESTAMP();")->execute([$usrIp, $properties, $properties]);
    }
}