<?php

function _jesyncphp_autoload_funcs($class) {    
    if (substr($class, 0, strlen(__NAMESPACE__)) != __NAMESPACE__)
        return false;
    $path = $class.'.php';
    if (file_exists($path)){
        require_once($path);
    }else{
        return false;
    }
}

spl_autoload_register('_jesyncphp_autoload_funcs');