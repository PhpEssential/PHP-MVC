<?php

namespace framework\exception;

class IllegalAccessException extends \Exception {
	public function __construct($message="", $previous=null) {
		parent::__construct("Non autorisé !" . ($message != "" ? " : " . $message : ""), null, $previous);
	}
}

