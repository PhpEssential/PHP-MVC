<?php

namespace phpessential\mvc\utils;

class StringUtils {

    /**
     * Check if string $haystack start with $needle
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return boolean
     */
    public static function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Check if string $haystack end with $needle
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return boolean
     */
    public static function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, - $length) === $needle);
    }

}
