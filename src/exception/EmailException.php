<?php
namespace phpessential\mvc\exception;

class EmailException extends \Exception {

	/**
	 *
	 * @param string $message
	 * @param \Exception $previous
	 */
	public function __construct(string $message, \Exception $previous) {
		parent::__construct($message, null, $previous);
	}
}