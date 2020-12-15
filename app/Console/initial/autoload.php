<?php

// special autoload for console commands
spl_autoload_register(function($className) {

    //$file = dirname(__DIR__) . '\\app\\' . $className . '.php';
    //$file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
    //echo $file;
    //if (file_exists($file)) {
    //    include $file;
    //}

    $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
    include_once './' . $className . '.php';

});
