<?php
namespace framework\sql\core\metadata;

use framework\sql\models\Entity;

/**
 * Represent an association between two entity
 * 
 */
class DirectAssociation extends AbstractAssociation {
    
	/**
	 * 
	 * @param string $name
	 * @param Entity $foreignClass
	 * @param LinkField[] $foreignFields
	 */
	function __construct(string $name, $foreignClass, array $foreignFields) {
    	parent::__construct($name, $foreignClass, $foreignFields);
    }
}