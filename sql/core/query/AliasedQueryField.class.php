<?php 
namespace framework\sql\core\query;


use framework\sql\core\metadata\Field;

/**
 * champs sql associé à une table
 *
 * @author Vince
 *
 */
class AliasedQueryField extends QueryField {
    
	
	/**
	 * Alias du champ SQL
	 *
	 * @var string
	 */
	private $alias = null;
	
    /**
     * 
     * @param AliasedTable $table
     * @param Field $field
     * @param string $path
     */
    function __construct(AliasedTable $table, Field $field, string $path){
    	parent::__construct($table, $field);
    	$this->alias = ($path == "" ? $field->name : $path . "-" . $field->name);
    }
    
    public function getAlias() : string {
    	return $this->alias;
    }
    
    public function toString() {
    	return $this->getAccessString() . ' "' . $this->alias . '"';
    }
}