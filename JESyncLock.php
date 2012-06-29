<?php
/**
 * Represents a lock key status
 *
 * @author julio
 */
class JESyncLock {
    private $key;
    private $concurrent;
    private $maxConcurrent;
    private $waiting;
    private $locked;
    private $socket;
    private $expiresAt;
    private $jesync;
    
    public function __construct($key,$concurrent,$waiting,$locked,$timeoutExpires,$jesync,$socket) {
        $this->key=$key;
        $this->concurrent=$concurrent;
        $this->waiting=$waiting;
        $this->locked=$locked;
        $this->socket=$socket;
        $this->expiresAt=time()+$timeoutExpires;
        $this->jesync=$jesync;
    }
    
    public function getKey() {
        return $this->key;
    }

    public function getConcurrent() {
        return $this->concurrent;
    }

    public function setMaxConcurrent($maxConcurrent) {
        $this->maxConcurrent = $maxConcurrent;
    }

    public function getMaxConcurrent() {
        return $this->maxConcurrent;
    }

    public function getWaiting() {
        return $this->waiting;
    }
    
    public function isLocked(){
        return $this->locked;
    }

    public function getSecondsRemaining(){
        $res= $this->expiresAt - time();
        return $res>=0?$res:0;
    }

    /**
     * Releases the lock on the key. This can only be called if the lock was
     * granted to the instance on which the method is being called
     * @return bool true if the lock was released
     * @throws Exception 
     */
    public function release(){
        if(!$this->locked){
            throw new Exception('The lock handle is not locked');
        }
        
        fputs($this->socket, sprintf("release %s\n",$this->key));
        $res=explode(' ',fgets($this->socket));
        $this->concurrent=(int)$res[1];
        $this->waiting=(int)$res[2];
        return $res[0]=='RELEASED';
    }

    /**
     * Used to renew a lock handle. This function will return a JESyncLock object, which should be used afterwards
     * The original JESyncLock object will be marked as not owner of the lock and should not be used
     * @return JESyncLock
     */
    public function renew(){
        $this->locked=false;
        return $this->jesync->lock($this->key,$this->maxConcurrent,0);
    }

}

