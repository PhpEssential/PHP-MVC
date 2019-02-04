<?php
namespace framework\sql\core\metadata;


use framework\sql\models\Entity;

/**
 * Represent a foreign key
 */
class OneToOneRef extends DirectAssociation {
	
    /**
     * 
     * @param Entity $foreignClass
     * @param string $name
     * @param array $fieldMap
     */
    function __construct($foreignClass, string $name, array $fieldMap) {
    	parent::__construct($name, $foreignClass, self::createLinkFields($fieldMap, $foreignClass));
    }
}

