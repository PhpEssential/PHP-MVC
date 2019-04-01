<?php
namespace tse\mvc\utils;

class LogUtils {

	public static function error(\Exception $e) {
		$str = self::logError($e, "\n");
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

	private static function logError(\Exception $e, $str = "") {
		$str .= "[ERROR] " . $e->getMessage() . "\n";
		$str .= "[ERROR] " . $e->getTraceAsString() . "\n";
		if ($e->getPrevious() != null) {
			$str = self::logError($e->getPrevious(), $str);
		}
		return $str;
	}
}

