<?php
namespace framework;

use tse\mvc\utils\LogUtils;
use tse\mvc\utils\StringUtils;

/**
 * Configuration access
 *
 * The configuration file must be place in "conf" folder which must be at root of project
 */
class Config {

	/*
	 * Default configuration keys
	 */
	const DB_HOST = "db_host";
	const DB_NAME = "db_name";
	const DB_USER = "db_user";
	const DB_PASSWORD = "db_password";
	const ROOT_URL = "root_url";
	const ROOT_PATH = "root_path";
	const EMAIL_INFO = "email_info";
	const DEFAULT_LOCAL = "default_local";
	const DEFAULT_TIMEZONE = "default_timezone";
	const ENV_TYPE = "env";
	const LOG_ACTIVE = "log_active";
	const LOG_FILE = "log_file";

	/**
	 * Singleton's instance
	 *
	 * @var Config
	 */
	private static $instance;

	/**
	 * Configuration values
	 *
	 * @var array
	 */
	private $values;

	/**
	 * Load ini file
	 */
	function __construct() {
		if (! file_exists(APPLICATION_CONFIGURATION_FILE)) {
			LogUtils::error("configuration file not found, check your APPLICATION_CONFIGURATION_FILE path");
		}
		$this->values = parse_ini_file(APPLICATION_CONFIGURATION_FILE);
	}

	private static function getInstance(): Config {
		if (self::$instance === null) {
			self::$instance = new Config();
		}
		return self::$instance;
	}

	public static function loadConfiguration() {
		// Set log configuration
		ini_set('log_errors', (self::has(self::LOG_ACTIVE) && self::get(self::LOG_ACTIVE) === "true" ? "On" : "Off"));
		if (self::has(self::LOG_FILE)) {
			$logFile = self::get(self::LOG_FILE);
			if (! StringUtils::startsWith($logFile, DIRECTORY_SEPARATOR)) {
				$logFile = APPLICATION_ROOT . $logFile;
			}
			ini_set('error_log', $logFile);
		}

		// Set error configuration
		if (! self::isProd()) {
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
		} else {
			ini_set('display_errors', 0);
			error_reporting(0);
		}
	}

	public static function isDev() {
		return self::get(self::ENV_TYPE) == "dev";
	}

	public static function isTest() {
		return self::get(self::ENV_TYPE) == "test";
	}

	public static function isProd() {
		return self::get(self::ENV_TYPE) == "prod";
	}

	public static function has(string $key): string {
		return array_key_exists($key, self::getInstance()->values);
	}

	/**
	 * Get configuration value
	 *
	 * @param string $key
	 *        	- configuration key
	 *
	 * @return string configuration value
	 */
	public static function get(string $key) {
		if (self::has($key)) {
			return self::getInstance()->values [$key];
		} else {
			LogUtils::warning("key not found in " . APPLICATION_CONFIGURATION_FILE . " file : " . $key);
			return null;
		}
	}
}