<?php
/**
 * Description of JESyncLock
 *
 * @author julio
 */
class JESyncLock {
    private $key;
    private $concurrent;
    private $waiting;
    private $locked;
    private $socket;
    
    public function __construct($key,$concurrent,$waiting,$locked,$socket) {
        $this->key=$key;
        $this->concurrent=$concurrent;
        $this->waiting=$waiting;
        $this->locked=$locked;
        $this->socket=$socket;
    }
    
    public function getKey() {
        return $this->key;
    }

    public function getConcurrent() {
        return $this->concurrent;
    }

    public function getWaiting() {
        return $this->waiting;
    }
    
    public function isLocked(){
        return $this->locked;
    }

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
    
}

