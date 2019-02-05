<?php 
namespace framework\sql\core\metadata;

/**
 * Représentation d'une table SQL utilisée dans une requête SELECT avec un alias
 */
class Table {
	
    /**
     * Name in database
     * 
     * @var string
     */
    private $sqlName;
    
    public function __construct(string $sqlName) {
        $this->sqlName = $sqlName;
    }
    
    public function getName() : string {
    	return $this->sqlName;
    }
}