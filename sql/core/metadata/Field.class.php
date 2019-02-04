<?php
namespace framework\sql\core\metadata;

/**
 * Represent a database's field link to a model's field
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
     * Nom du champ dans la bdd
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
    
    function __construct(string $name, string $sqlName, int $dataType) {
    	parent::__construct($name);
        $this->sqlName = $sqlName;
        $this->dataType = $dataType;
    }
}

