<?php
namespace framework\sql\core\metadata;

use framework\sql\models\Entity;

/**
 * Represent a one to many association
 */
class OneToMany extends ExtraAssociation {
	
	public $foreignFieldName;
	
	/**
	 * 
	 * @param string $name
	 * @param Entity $primaryClass
	 * @param Entity $foreignClass
	 * @param string $foreignFieldName
	 */
    function __construct(string $name, $foreignClass, string $foreignFieldName) {
    	parent::__construct($name, $foreignClass, $foreignClass::findField($foreignFieldName)->linkFields);
    	$this->foreignFieldName = $foreignFieldName;
    }
}