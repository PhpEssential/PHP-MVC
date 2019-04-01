<?php
namespace tse\mvc\exception;

class SqlException extends \Exception {

	/**
	 *
	 * @param string $sqlRequest
	 * @param \Exception $previous
	 */
	public function __construct($sqlRequest, $previous) {
		parent::__construct("Problème lors de l'execution de la rêquete: " . $sqlRequest . "\n" . $previous->getMessage());
	}
}