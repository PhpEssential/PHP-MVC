<?php

namespace framework\exception;

class EmailException extends \Exception {
	public function __construct(string $message, \Exception $previous) {
		parent::__construct($message, null, $previous);
	}
}

