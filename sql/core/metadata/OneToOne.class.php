<?php
namespace framework\sql\core\metadata;


use framework\sql\models\Entity;

/**
 * Represent a foreign key
 */
class OneToOne extends DirectAssociation {
	
	public $foreignFieldName;
	
    /**
     * 
     * @param string $name
     * @param Entity $foreignClass
     * @param array $foreignFieldName
     */
	function __construct(string $name, $foreignClass, string $foreignFieldName) {
		parent::__construct($name, $foreignClass, $foreignClass::findField($foreignFieldName)->linkFields);
		$this->foreignFieldName = $foreignFieldName;
    }
}