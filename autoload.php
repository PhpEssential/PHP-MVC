<?php

// Autoloader
spl_autoload_register( function ($className) {
    $className = ltrim($className, "\\");
    $fileName  = "";
    $namespace = "";
    if ($lastNsPos = strrpos($className, "\\")) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace("\\", DIRECTORY_SEPARATOR, str_replace("/", DIRECTORY_SEPARATOR, $namespace)) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.class.php';

    if(file_exists(APPLICATION_ROOT . DIRECTORY_SEPARATOR . $fileName)) {
    	require_once APPLICATION_ROOT . $fileName;
    } else {
    	error_log("[ERROR] File not exisit: " . APPLICATION_ROOT . $fileName);
    }
});
?>