<?php

function _jesyncphp_autoload_funcs($class) {    
    if (substr($class, 0, strlen(__NAMESPACE__)) != __NAMESPACE__)
        return false;
    //$class=substr($class, strlen("JESync"));
    
    $path = sprintf(
            '%s/%s.php', __DIR__ , str_replace("\\", '/', $class)
    );
    
    if (file_exists($path)){
        require_once($path);
    }else{
        return false;
    }
}




spl_autoload_register('_jesyncphp_autoload_funcs');