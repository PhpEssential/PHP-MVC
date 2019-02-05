<?php
namespace framework\sql\core\metadata;


use framework\sql\models\Entity;

/**
 * Represent a many to one association
 */
class ManyToOne extends DirectAssociation {
    
    /**
     * 
     * @param Entity $foreignClass
     * @param string $name
     * @param array $fieldMap
     * @param int $dir
     */
    function __construct($foreignClass, string $name, array $fieldMapping) {
    	parent::__construct($name, $foreignClass, self::createLinkFields($fieldMapping));
    }
}