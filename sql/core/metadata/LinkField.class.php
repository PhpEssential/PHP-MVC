<?php
namespace framework\sql\core\metadata;

/**
 * Represent a database's field link to a model's field
 */
class LinkField {

	/**
     * Nom du champ dans la bdd
     *
     * @var string
     */
    public $sqlName;
    
    /**
     * champ associé
     * 
     * @var Field
     */
    public $field;
    
    function __construct(Field $field, string $sqlName) {
        $this->sqlName = $sqlName;
        $this->field = $field;
    }
}

