<?php 
namespace framework\sql\core\query;

use framework\sql\core\metadata\Table;

/**
 * Représentation d'une table SQL utilisée dans une requête SELECT avec un alias
 */
class AliasedTable {

    /**
     * Alias
     * 
     * @var string
     */
    private $alias;
    
    /**
     * String path
     *
     * @var string
     */
    private $path;
    
    /**
     * SQL Table 
     * 
     * @var Table
     */
    private $table;
    
    public function __construct(string $alias, string $path, Table $table) {
        $this->alias = $alias;
        $this->path = $path;
        $this->table = $table;
    }
    
    public function getAlias() : string {
    	return $this->alias;
    }
    
    public function getPath() : string {
    	return $this->path;
    }
    
    public function getTable() : Table {
    	return $this->table;
    }
}