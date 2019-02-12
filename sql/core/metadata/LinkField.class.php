<?php
namespace framework\sql\core\metadata;

/**
 * Represent a database's field link to a model's field
 */
class LinkField {
	
	/**
	 * Database link field name
	 *
	 * @var string
	 */
	public $sqlName;
	
	/**
	 * Reference field
	 *
	 * @var Field
	 */
	public $referenceField;

	/**
	 *
	 * @param Field $referenceField
	 *        	- Database ilnk field name
	 * @param string $sqlName
	 *        	- Reference field
	 */
	function __construct(Field $referenceField, string $sqlName) {
		$this->sqlName = $sqlName;
		$this->referenceField = $referenceField;
	}
}