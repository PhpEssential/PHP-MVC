<?php
namespace framework\system;

class System {

	public static function isUnixOs(): bool {
		return (DIRECTORY_SEPARATOR == '/') ? true : false;
	}
}