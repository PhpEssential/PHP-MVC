<?php
namespace phpessential\mvc\utils;

/**
 * Security functions
 */
class SecurityUtils {

	/**
	 * Token generation
	 *
	 * @param int $length
	 *        	- 64 minimum
	 *
	 * @return string
	 */
	public static function generateToken(int $length): string {
		return bin2hex(openssl_random_pseudo_bytes($length));
	}

	/**
	 * Hash generation
	 *
	 * @param string $text
	 *        	- text to encrypt
	 * @param string $salt
	 *        	- salt encrypted in md5
	 *
	 * @return string
	 */
	public static function hash($text, $salt) {
		return crypt($text, md5($salt));
	}
}