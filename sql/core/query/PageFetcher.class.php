<?php 
namespace framework\sql\core\query;

use framework\Config;

/**
 * Représentation d'une requête SQL de type SELECT avec clause LIMIT
 *
 */
class PageFetcher extends Fetcher {
	
	/**
	 * 
	 * @var string
	 */
	private $filter = null;
	
	/**
	 * 
	 * @var QueryField[]
	 */
	private $filterFields = array();
	
	/**
     * @param AliasedTable $table
     * @param int $pageSize
     * @param int $startIndex
     */
	function __construct(string $entityClass, int $pageSize, int $startIndex) {
		parent::__construct($entityClass);
        $this->select->setLimit($pageSize);
        $this->select->setOffset($startIndex);
    }
    
    /**
     * 
     * @param string $filter
     * @param QueryField[] $fields
     */
    public function setFilter(string $filter, array $fields) : PageFetcher {
    	$this->filter = $filter;
    	foreach ($fields as $field) {
    		$this->filterFields[] = $field;
    	}
    	
    	return $this;
    }
    
    public function findPage($conn = null) : PagedList {
    	$conn = $this->getConnection($conn);
    	$countSelect = $this->select->cloneWithLinksAndConditions();
    	$countSelect->aggregateField($this->entityClass::discriminantFields()[0], "COUNT");
    	$count = $conn->execute($countSelect->toString(Config::get(Config::DB_NAME)))[0][0];

    	if(count($this->filterFields) != 0) {
    		$this->select->conditions->disjonction();
    		foreach ($this->filterFields as $filterField) {
    			$this->select->conditions->like($filterField, $this->filter);
    		}
    		$this->select->conditions->endJonction();
    	}
    	$fitredCountSelect = $this->select->cloneWithLinksAndConditions();
    	$fitredCountSelect->aggregateField($this->entityClass::discriminantFields()[0], "COUNT");
    	$filtredCount = $conn->execute($fitredCountSelect->toString(Config::get(Config::DB_NAME)))[0][0];
    	
    	$list = $this->findList($conn);
    	return new PagedList($list, $count, $filtredCount);
    }
}
?>