<?php
namespace phpessential\mvc\utils;

/**
 * Classe utilitaire pour la gestion des cookies
 */
class CookieUtils {

	public static function get(string $key): string {
		return $_COOKIE [$key];
	}

	public static function set(string $key, string $value, int $expire, string $path = "/") {
		setcookie($key, $value, $expire, $path);
	}

	public static function unset(string $key, string $path = "/") {
		unset($_COOKIE [$key]);
		setcookie($key, '', time() - 3600, $path);
	}

	public static function contains(string $key): bool {
		return isset($_COOKIE [$key]);
	}
}