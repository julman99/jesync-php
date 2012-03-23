<?php
/**
 * Class used to connect to a JESync server and request a lock
 *
 * @author julio
 */
class JESync {

    private $servers = array();
    private $sockets = array();
    private $token;

    public function __construct() {
        $this->token = uniqid();
    }

    private function getSocket($key) {
        $hash = md5($key);
        $mod = count($this->servers);
        $num = intval(substr($hash, 0, 3), 16);
        $server = $this->servers[$num % $mod];
        if (!isset($this->sockets[$server])) {
            $this->sockets[$server] = fsockopen($server, 11400);
        }
        return $this->sockets[$server];
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
     * @return JESyncLock 
     */
    public function lock($key, $maxConcurrent = 1, $timeout = -1) {
        $f = $this->getSocket($key);
        
        fputs($f, sprintf("lock %s %d %d\n", $key, $maxConcurrent, $timeout));
        $res = explode(' ', fgets($f));

        $handle = new JESyncLock($key, (int) $res[1], (int) $res[2], $res[0] == 'GRANTED',$f);
        return $handle;
    }
}

