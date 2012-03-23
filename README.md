# JESync-php
JESync-php is the php driver to connect to the [JESync Server](https://github.com/julman99/JESync)

# Usage
    <?php
    include 'jesync-php.php'; //includes auto loading stuff
    $je=new JESync();
    $je->addServer('jesync1.foo');
    $je->addServer('jesync2.foo');

    $key='akey';
    $timeout=4; //seconds
    $maxConcurrent=1; //only 1 lock can be granted at a time for the key

    $h=$j->lock($key,$maxConcurrent,$timeout);
    if($h->isLocked()){
        //Yeeehaa we have the lock
        $h->release(); //lock released
    }else{
        // :( lock request timed out
    }

# Multiple Servers
You can use multiple servers with a single JESync object. When lock() is called, the key is hashed against the server list and the key is locked in that particular server only. It is a simple way to "horizontally scale" inspired in memcached mechanism.

# Status
This driver stills under alpha version, has not been tested and is not deployed on any live environment.

Real-world testing are planned to be done by the end of March 2012, and the stable release by Mid-April 2012