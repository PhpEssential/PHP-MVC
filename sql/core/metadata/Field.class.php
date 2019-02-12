<?php
namespace framework\sql\core\metadata;

/**
 * Represent a database's field link to an entity's attribute
 */
class Field extends AbstractField {
	
	/*
	 * Types de données pris en charge par l'ORM
	 */
	const TEXT = 0;
	const INT = 1;
	const DATE = 2;
	const DATE_TIME = 3;
	const BOOLEAN = 4;
	const FLOAT = 5;
	
	/**
	 * Database field name
	 *
	 * @var string
	 */
	public $sqlName;
	
	/**
	 * Type de la donnée
	 *
	 * @var int
	 */
	public $dataType;

	/**
	 *
	 * @param string $name
	 *        	- Entity attribute name
	 * @param string $sqlName
	 *        	- Database field name
	 * @param int $dataType
	 *        	- Data type
	 */
	function __construct(string $name, string $sqlName, int $dataType) {
		parent::__construct($name);
		$this->sqlName = $sqlName;
		$this->dataType = $dataType;
	}
}