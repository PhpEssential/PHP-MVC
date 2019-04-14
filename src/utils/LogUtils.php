<?php

namespace phpessential\mvc\utils;

use \Exception;

class LogUtils {

    public static function error($e) {
        if($e instanceof Exception) {
            $str = self::logError($e, "\n");
        } else {
            $str .= "[ERROR] " . $e . "\n";
        }
        error_log($str);
    }

    public static function info(string $message) {
        error_log("[INFO] " . $message);
    }

    public static function warning(string $message) {
        error_log("[WARNING] " . $message);
    }

    public static function debug(string $message) {
        error_log("[DEBUG] " . $message);
    }

    private static function logError(Exception $e, $str = "") {
        $str .= "[ERROR] " . $e->getMessage() . "\n";
        $str .= "[ERROR] " . $e->getTraceAsString() . "\n";
        if ($e->getPrevious() != null) {
            $str = self::logError($e->getPrevious(), $str);
        }
        return $str;
    }

}
