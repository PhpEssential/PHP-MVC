<?php
namespace framework\i18n;

use framework\Config;
use framework\utils\LogUtils;

class Message {

	/**
	 * Error keys
	 */
	const ERROR_UNAUTHORIZED = "error.unauthorized";
	const ERROR_UNEXPECTED = "error.unexpected";
	const ERROR_BAD_FORMAT = "error.bad.format";

	/**
	 * Data manipulation keys
	 */
	const SUCCESS_SAVE = "message.success.save";
	const SUCCESS_UPDATE = "message.success.update";
	const SUCCESS_DELETE = "message.success.delete";

	/**
	 *
	 * @var array[string, Message]
	 */
	private static $instances = array ();

	/**
	 *
	 * @var string
	 */
	private $local;

	/**
	 *
	 * @var array[string, string]
	 */
	private $params = array ();

	private function __construct(string $fileName, string $local) {
		$filePath = APPLICATION_ROOT . "conf" . DIRECTORY_SEPARATOR . $fileName . ".ini";
		if (file_exists($filePath)) {
			$this->params = parse_ini_file($filePath);
		} else {
			LogUtils::warning("file not found: " . $filePath);
		}
	}

	private static function getInstance(string $local, string $fileName) {
		$fullName = $fileName . "_" . $local;
		$messages = null;
		if (array_key_exists($fullName, self::$instances)) {
			$messages = self::$instances [$fullName];
		} else {
			$messages = new Message($fullName, $local);
			self::$instances [$fullName] = $messages;
		}
		return $messages;
	}

	public static function get(string $key, array $params = array(), string $local = null, string $fileName = "messages") {
		if ($local == null) {
			$local = Config::get(Config::DEFAULT_LOCAL);
		}
		$messages = self::getInstance($local, $fileName);
		if (array_key_exists($key, $messages->params)) {
			$message = $messages->params [$key];
			$paramsCount = count($params);
			for($i = 0; $i < $paramsCount; $i ++) {
				$message = str_replace("\$" . $i, $params [$i], $message);
			}
			return $message;
		} else {
			LogUtils::debug("key - " . $key . " not found in file - " . $fileName . "_" . $local);
			return $key;
		}
	}
}