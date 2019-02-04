<?php 
namespace framework\sql\core\query;

use framework\sql\core\metadata\Table;
use framework\sql\utils\SqlUtils;
use framework\sql\core\metadata\Field;

/**
 * Represent a SQL INSERT instructions which is used to insert entity's data into database
 */
class Insert {
	
    /**
     * Entities fields
     * 
     * @var array[Field, mixed]
     */
	private $fieldValueMap = array();
    
    /**
     * Entity table
     * 
     * @var Table
     */
    private $table;
    
    /**
     * 
     * @param Table $table
     * @param array $fieldValueMap
     */
    function __construct(Table $table, array $fieldValueMap) {
    	$this->table = $table;
    	$this->fieldValueMap = $fieldValueMap;
    }
    
    /**
     * Build SQL query
     * 
     * @return string
     */
    public function toString(string $dbName) {
        $sqlFields = "";
        $sqlValues = "";
        foreach ($this->fieldValueMap as $fieldName => $fieldValueEntry) {
        	$field = $fieldValueEntry[0];
        	$value = $fieldValueEntry[1];
        	$sqlFields .= $fieldName . ",";
        	$sqlValues .= SqlUtils::addQuote($field, SqlUtils::ensureSqlValue($field, $value)) . ",";
        }
        $sqlFields = substr($sqlFields, 0, -1);
        $sqlValues = substr($sqlValues, 0, -1);
        
        return "INSERT INTO " . $dbName . "." . $this->table->getName() . "(" . $sqlFields . ") VALUES(" . $sqlValues . ")";
    }
}
?>