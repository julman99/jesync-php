<?php
/**
 * Class used to connect to a JESync server and request a lock
 *
 * @author julio
 */
class JESync {

    private static $sockets = array();
    private $servers = array();

    private function getSocket($key) {
        $count=count($this->servers);
        if($count>0){
            if($count>1){
                //More than 1 server, hash the key to select the appropiate one
                $hash = md5($key);
                $mod = count($this->servers);
                $num = intval(substr($hash, 0, 3), 16);
                $server = $this->servers[$num % $mod];
            }else{
                //Only one server, no need to hash
                $server=$this->servers[0];
            }
            //TODO:check for port on the server string <server>[:<port>]            
            if (!isset(self::$sockets[$server])) {
                self::$sockets[$server] = fsockopen($server, 11400);
            }
            return self::$sockets[$server];
        }else{
            throw new Exception('No servers added to the server pool');
        }
    }

    /**
     * Adds a server to the server connection pool 
     * @param string $server 
     */
    public function addServer($server) {
        $this->servers[] = $server;
    }

    /**
     * Request the lock for a key
     * If there is more than a server in the connection pool the key will be hashed
     * and will be locked in one of the servers only 
     * @param string $key
     * @param int $maxConcurrent
     * @param int $timeout
     * @param int $timeoutExpires The amount of seconds the lock will be granted
     * @return JESyncLock 
     */
    public function lock($key, $maxConcurrent = 1, $timeout = -1, $timeoutExpires = 120) {
        $f = $this->getSocket($key);
        if($f){
            fputs($f, sprintf("lock %s %d %d %d\n", $key, $maxConcurrent, $timeout, $timeoutExpires));
            $res = explode(' ', fgets($f));

            $handle = new JESyncLock($key, (int) $res[1], (int) $res[2], $res[0] == 'GRANTED', $res[4],$this , $f);
            return $handle;
        }else{
            return false;
        }
    }

}

