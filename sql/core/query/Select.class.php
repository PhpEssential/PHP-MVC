<?php 
namespace framework\sql\core\query;

use framework\exception\IllegalArgumentException;
use framework\sql\core\metadata\Field;
use framework\sql\core\metadata\Table;

/**
 * Represent a SQL Select instructions which is used to load entity's data
 */
class Select {
	
    /**
     * Selected entities fields
     * 
     * @var array[string][AliasedQueryField]
     */
	private $fields = array();
    
    /**
     * Aliased entity table in FROM clause
     * 
     * @var AliasedTable
     */
    private $table;
    
    /**
     * Represent JOIN clause
     * 
     * @var array[string][Join]
     */
    private $links = array();
    
    /**
     * 
     * @var int|null
     */
    private $limit = null;
    
    /**
     * 
     * @var int|null
     */
    private $offset = null;
      
    /**
     * Represent WHERE clause
     * 
     * @var ConditionExpression
     */
    public $conditions;
    
    /**
     * Represent ORDER BY clause
     *
     * @var OrderedField[]
     */
    private $orderedFields = array();
    
    /**
     * Counter for table alias generation
     * 
     * @var integer
     */
    private $tableAliasCount = 0;
    
    /**
     * @param Table $table	- Entity table
     */
    function __construct(Table $table) {
    	$this->table = new AliasedTable("t0", "", $table);
    	$this->conditions = new ConditionExpression();
    }
    
    /**
     * Get all selected fields
     * 
     * @return AliasedQueryField[]
     */
    public function getFields() {
    	return array_values($this->fields);
    }
    
    /**
     * Get primary table
     * 
     * @return AliasedTable
     */
    public function getTable(){
    	return $this->table;
    }
    
    /**
     * /!\ The table must be add before with addLink()
     * 
     * Add field to selection
     *
     * @param Field $field
     * @param string $path
     *
     * @return Select
     */
    public function addField(Field $field, string $path = "") : Select {
    	$this->addFields(array($field), $path);
    	return $this;
    }
    
    /**
     * /!\ The table must be add before with addLink()
     * 
     * Add fields to selection.
     *
     * @param Field[] $fields
     * @param string $path
     *
     * @return Select
     */
    public function addFields(array $fields, string $path = "") : Select {
    	$table = $this->findTable($path);
    	foreach ($fields as $field) {
    		$aliasedField = new AliasedQueryField($table, $field, $path);
    		$this->fields[$aliasedField->getAlias()] = $aliasedField;
    	}
    	return $this;
    }
    
    /**
     * Add aggregate field to selection
     *
     * @param AliasedTable $table
     * @param Field $field
     * @param string $function
     * @param string $path
     *
     * @return Select
     */
    public function aggregateField(Field $field, string $function, string $path = "") : Select {
    	$alias = ($path == "" ? $field->name : $path. "-" . $field->name);  
    	$this->fields[$alias] = new AggregateQueryField($function, $this->table, $field, $alias, $path);
    	return $this;
    }
    
    /**
     * Add link table
     * 
     * @param string|array $path
     * @param AliasedTable $table
     * @param ConditionExpression $conditions
     * @param string $linkType
     * @return Select
     */
    public function addLink(AliasedTable $table, ConditionExpression $conditions, bool $strict) : Select {
    	$this->links[$table->getPath()] = new Join(
    		$strict ? "JOIN" : "LEFT OUTER JOIN", 
    		$table, 
    		$conditions
    	);
    	return $this;
    }
    
    public function addOrderedField(string $path, string $orderDirection) {
    	$field = $this->findField($path);
    	$this->orderedFields[] = new OrderedField($field, $orderDirection);
    	return $this;
    }
    
    /**
     * Find table in current select statement
     * 
     * @param string $path
     * @throws IllegalArgumentException
     * @return AliasedTable
     */
    public function findTable(string $path = "") : AliasedTable {
    	if($path == "")
    		return $this->table;
    	
    	if(array_key_exists($path, $this->links))
    		return $this->links[$path]->table;
    	
    	throw new IllegalArgumentException("path", "no table for this path: " . $alias);
    }
    
    /**
     * 
     * @param Field[] $fields
     * @param AliasedTable $table
     * 
     * @return AliasedQueryField[]
     */
    public function findFields(array $fields, AliasedTable $table) : array {
    	$results = array();
    	foreach ($fields as $field) {
    		if($table->getPath() == "") {
    			$results[] = $this->findField($field->name);
    		} else {
    			$results[] = $this->findField($table->getPath() . "-" . $field->name);
    		}
    	}
    	return $results;
    }
    
    /**
     * 
     * @param string $path
     * @return AliasedQueryField
     */
    public function findField(string $path) : AliasedQueryField {
    	return $this->fields[$path];
    }
    
    public function hasField(string $path) {
    	return array_key_exists($path, $this->fields);
    }
    
    /**
     * 
     * @param string $path
     * @param Table $table
     * @return AliasedTable
     */
    public function createAliasedTable(string $path, Table $table) : AliasedTable {
    	return new AliasedTable($this->generateTableAlias(), $path, $table);
    }
    
    public function setLimit(int $limit) {
    	$this->limit = $limit; 
    }
    
    public function setOffset(int $offset) {
    	$this->offset = $offset;
    }
    
    public function cloneWithLinksAndConditions() : Select {
    	$select = new Select($this->table->getTable());
    	$select->links = $this->links;
    	$select->tableAliasCount = $this->tableAliasCount;
    	$select->conditions = $this->conditions;
    	
    	return $select;
    }
    
    /**
     * Contruit la chaîne SQL
     * 
     * @return string
     */
    public function toString(string $dbName) {
        $sql = "SELECT ";
        foreach ($this->fields as $alias => $field) {
        	$sql .= $field->toString() . ",";
        }
        $sql = substr($sql, 0, strlen($sql) - 1);
        
        $sql .= " FROM " . $dbName . "." . $this->table->getTable()->getName() . " " . $this->table->getAlias() . " ";
        foreach ($this->links as $tableKey => $link) {
        	$sql .= $link->toString($dbName) . " ";
        }

        $conditionStr = $this->conditions->toString();
        $sql .= ($conditionStr == "") ? "" : "WHERE " . $conditionStr;
        
        $orderClause = "";
        foreach ($this->orderedFields as $orderedField) {
        	$orderClause .= $orderedField->toString() . ",";
        }
        $sql .= ($orderClause == "") ? "" : "ORDER BY " . substr($orderClause, 0, strlen($orderClause) - 1). " ";
        
        $limitClause = "";
        if($this->limit != null && $this->offset != null) {
        	$limitClause = $this->limit . "," . $this->offset;
        } else if($this->limit != null) {
        	$limitClause = $this->limit;
        } else if($this->offset != null) {
        	$limitClause = $this->offset . ",18446744073709551615";
        }
        $sql .= ($limitClause == "") ? "" : "LIMIT " . $limitClause . " ";
        
        return $sql;
    }
    
    /**
     * 
     * @param Field[] $fields
     * @param AliasedTable $table
     * 
     * @return QueryField[]
     */
    private function createQueryFields(array $fields, AliasedTable $table) {
    	$result = array();
    	foreach ($fields as $field) {
    		$result[] = new QueryField($table, $field);
    	}
    }
    
    private function generateTableAlias() {
    	return "t" . ++$this->tableAliasCount;
    }
}
?>