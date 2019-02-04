<?php
namespace framework\sql\core\metadata;

/**
 * Represent an entity attribute
 * 
 */
class AbstractField {
    
    /**
     * Entity attribute name
     *
     * @var string
     */
    public $name;
    
    function __construct(string $name) {
    	$this->name = $name;
    }
}

