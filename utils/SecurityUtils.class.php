<?php
namespace framework\utils;

class SecurityUtils {
	
	/**
	 * 
	 * @param int $length 64 minimum
	 * @return string
	 */
	public static function generateToken(int $length) : string {
		return bin2hex(openssl_random_pseudo_bytes($length));
	}
	
	public static function hash($text, $salt){
		return crypt($text, md5($salt));
	}
}