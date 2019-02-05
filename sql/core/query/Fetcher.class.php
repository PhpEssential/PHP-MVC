<?php 
namespace framework\sql\core\query;

use framework\Config;
use framework\exception\IllegalArgumentException;
use framework\exception\SqlException;
use framework\sql\core\DbConnection;
use framework\sql\core\metadata\DirectAssociation;
use framework\sql\core\metadata\ExtraAssociation;
use framework\sql\core\metadata\Field;
use framework\sql\core\metadata\ManyToMany;
use framework\sql\core\metadata\OneToMany;
use framework\sql\models\Entity;
use framework\sql\core\metadata\OneToOne;

/**
 * Represent one or many SQL Select instructions which are used to load model's data
 */
class Fetcher {
	
	/**
	 * Entity class
	 *
	 * @var string
	 */
	protected $entityClass;
	
    /**
     * Secondaries fetcher
     *
     * @var Fetcher[]
     */
    private $extras = array();
    
    /**
     * Primary select
     * 
     * @var Select
     */
    protected $select;
    
    /**
     * @param Entity $entityClass
     */
    function __construct($entityClass, bool $onlyDiscriminantFields = false) {
    	$this->entityClass = $entityClass;
    	$this->select = new Select($entityClass::table());
    	if ($onlyDiscriminantFields) {
    		$this->select->addFields($entityClass::discriminantFields());
    	} else {
    		$this->select->addFields($entityClass::getDirectFields());
    	}
    }
    
    /**
     * Load attriutes of current entity
     *
     * @param string|string[] $path 			- Path to foreign entity field
     * @param ConditionExpression $conditions	- Additional conditions
     * @param bool 	$onlyDiscriminants 	- If true that do not select all foreign entity's fields
     * @param bool 	$strict						- If true JOIN is generate else it is a LEFT OUTER JOIN
     *
     * @return Fetcher
     */
    public function fetch($path, bool $onlyDiscriminants = false, bool $strict = false) : Fetcher {
    	// transform and check parameter
    	$flatPath = "";
    	if(is_string($path)) {
    		$flatPath = $path;
    		$path = explode("-", $path);
    	} else {
    		$flatPath = implode("-", $path);
    	}
    	if($flatPath == "") {
    		throw new IllegalArgumentException("path", "empty path not allowed !");
    	}
    	
    	// Search corresponding select and adapt the search path
    	$fetcherData = $this->searchFetcher($flatPath);
    	if($fetcherData != null) {
    		$searchPath = $fetcherData[0];
    		$fetcher = $fetcherData[1];
    	} else {
    		$searchPath = $flatPath;
    		$fetcher = $this;
    	}
    	
    	$select = $fetcher->select;
    	// Find field for this path
    	$field = $fetcher->entityClass::findField($searchPath);
    	if($field instanceof Field) {
    		// Add the field
    		$fetcher->addField($field);
    	} else if ($field instanceof OneToOne) {
    		$primaryTable = $select->createAliasedTable($flatPath, $field->foreignClass::table());
    		$primaryFields = $field->foreignClass::discriminantFields();
    		$foreignFields = array();
    		foreach ($field->linkFields as $linkField) {
    			$foreignFields[] = $linkField->field;
    		}
    		$foreignTable = $select->findTable(implode("-", array_slice(explode("-", $flatPath), 0, -1)));
    		$fetcher->addDirectAssociation($searchPath, $field, $primaryTable, $primaryFields, $foreignTable, $foreignFields, $strict, $onlyDiscriminants);
    	} else if($field instanceof DirectAssociation) {
    		$primaryFields = array();
    		foreach ($field->linkFields as $linkField) {
    			$fieldName = $field->name . "-" . $linkField->field->name;
    			$primaryFields[] = new Field($fieldName, $linkField->sqlName, $linkField->field->dataType);
    		}
    		$primaryTable = $select->findTable(implode("-", array_slice(explode("-", $flatPath), 0, -1)));
    		$foreignTable = $select->createAliasedTable($flatPath, $field->foreignClass::table());
    		$foreignFields = $field->foreignClass::discriminantFields();
    		// Create the link and add foreign entity fields
    		$fetcher->addDirectAssociation($searchPath, $field, $primaryTable, $primaryFields, $foreignTable, $foreignFields, $strict, $onlyDiscriminants);
    	}  else if($field instanceof OneToMany) {
    		// Create extra fetcher with foreign table, and add it's fields
    		$fetcher->addOneToMany($searchPath, $field, $onlyDiscriminants, false);
    	} else if($field instanceof ManyToMany) {
    		// Create extra fetcher with foreign table, link with associative table and add foreign table's fields
    		$fetcher->addManyToMany($searchPath, $field, $onlyDiscriminants, false);
    	}

    	return $this;
    }
    
    /**
     * Create new link for the path
     *
     * @param string $path					- Path of field to fetch
     * @param DirectAssociation $field		- Field to fetch
     * @param array $primaryFields			- Field to fetch
     * @param array $foreignFields			- Field to fetch
     * @param bool 	$strict					- If true JOIN is generate else it is a LEFT OUTER JOIN
     * @param bool 	$onlyDiscriminantFields - If true that do not select all foreign entity's fields
     */
    private function addDirectAssociation(string $flatPath, DirectAssociation $field, AliasedTable $primaryTable, array $primaryFields, AliasedTable $foreignTable, array $foreignFields, bool $strict, bool $onlyDiscriminantFields) {
    	$select = $this->select;
    	
    	// Search discriminant fields of primary model
    	$primaryFields = $this->createQueryFields($primaryFields, $primaryTable);
    	
    	// Do the same for foreign model
    	$foreignFields = $this->createQueryFields($foreignFields, $foreignTable);
    	
    	// Create the condition
    	$conditions = new ConditionExpression();
    	foreach ($field->linkFields as $referenceField) {
    		foreach ($foreignFields as $foreignField) {
    			foreach ($primaryFields as $primaryField) {
    				if($foreignField->field->name == $referenceField->field->name
    					&& $primaryField->field->sqlName == $referenceField->sqlName) {
    					$conditions->eq($foreignField, $primaryField);
    				}
    			}
    		}
    	}
    	
    	// Add the link and fields to the select
    	if($field instanceof OneToOne) {
    		$select->addLink($primaryTable, $conditions, $strict);
    	} else {
    		$select->addLink($foreignTable, $conditions, $strict);
    	}
    	if ($onlyDiscriminantFields) {
    		$select->addFields($field->foreignClass::discriminantFields(), $flatPath);
    	} else {
    		$select->addFields($field->foreignClass::getDirectFields(), $flatPath);
    	}
    }
    
    private function addOneToMany(string $flatPath, OneToMany $field, bool $onlyDiscriminants = false) {
    	// Create extra fetcher
    	$newFetcher = new Fetcher($field->foreignClass, $onlyDiscriminants);
    	$referenceFields = array();
    	foreach ($field->linkFields as $linkField) {
    		$referenceFields[] = new Field($linkField->field->name, $linkField->sqlName, $linkField->field->dataType);
    	}
    	$newFetcher->select->addFields($referenceFields);
    	
    	$this->extras[$flatPath] = $newFetcher;
    	
    	return $this;
    }
    
    private function addManyToMany(string $flatPath, ManyToMany $field, bool $onlyDiscriminants = false) {
    	// Create extra fetcher
    	$newFetcher = new Fetcher($field->foreignClass, $onlyDiscriminants);
    		
   		// Search discriminant fields of forgein entity
    	$foreignTable = $newFetcher->select->findTable();
    	$foreignFields = $newFetcher->select->findFields($field->foreignClass::discriminantFields(), $foreignTable);
    		
   		// And the same for the implicit associative entity
    	$associativeTable = $newFetcher->select->createAliasedTable(md5($field->associativeTable->getName()), $field->associativeTable);
    	$associativeReferenceFields = array();
    	foreach ($field->linkFields as $linkField) {
    		$associativeReferenceFields[] = new Field($linkField->field->name, $linkField->sqlName, $linkField->field->dataType);
    	}
    	$associativeReferenceFields = $newFetcher->createQueryFields($associativeReferenceFields, $associativeTable);
    	
   		// Create conditions
   		$foreignCondition = new ConditionExpression();
   		foreach ($foreignFields as $foreignField) {
   			foreach ($associativeReferenceFields as $associativeTableField) {
   				if($foreignField->field->name == $associativeTableField->field->name) {
   					$foreignCondition->eq($foreignField, $associativeTableField);
   					break;
   				}
   			}
   		}
   		$newFetcher->select->addLink($associativeTable, $foreignCondition, true);
   		
   		$associativePrimaryFields = array();
   		foreach ($field->primaryFields as $linkField) {
   			$associativePrimaryFields[] = new Field($linkField->field->name, $linkField->sqlName, $linkField->field->dataType);
   		}
   		$newFetcher->select->addFields($associativePrimaryFields, md5($field->associativeTable->getName()));
    		
   		$this->extras[$flatPath] = $newFetcher;
    	
    	return $this;
    }
    
    /**
     * Search a field already fetch in this fetcher
     *
     * @param string|array $path
     * @return QueryField
     */
    public function findField($path) {
    	$flatPath = is_string($path) ? $path : implode("-", $path);
    	$fetcher = $this;
    	$searchPath = $flatPath;
    	$fetcherPath = $this->searchFetcher($flatPath);
    	if($fetcherPath != null) {
    		$searchPath = substr($flatPath, count($selectPath) + 1);
    		$fetcher = $this->extras[$selectPath];
    	}
    	
    	if($fetcher->select->hasField($searchPath)) {
    		return $fetcher->select->findField($searchPath);
    	} else {
    		$table = $fetcher->select->findTable(implode("-", array_slice(explode("-", $path), 0, -1)));
    		return new QueryField($table, $fetcher->entityClass::findField($searchPath));
    	}
    }
    
    /**
     * Search a table already fetch in this fetcher
     *
     * @param string|array $path
     * @return AliasedTable
     */
    public function findTable($path) {
    	$flatPath = is_string($path) ? $path : implode("-", $path);
    	$select = null;
    	$searchPath = null;
    	
    	$fetcherData = $this->searchFetcher($flatPath);
    	if($fetcherData == null)
    		return $this->select->findTable($flatPath);
    	else
    		return $fetcherData[1]->select->findTable($fetcherData[0]);
    }
    
    public function addOrderedField($path, string $orderDirection) : Fetcher {
    	$this->select->addOrderedField($path, $orderDirection);
    	return $this;
    }
    
    /**
     * 
     * @param string $path
     * @return ConditionExpression
     */
    public function findConditions($path = "") : ConditionExpression {
    	return $this->getFetcher($path)->select->conditions;
    }
    
    /**
     * 
     * @param DbConnection $conn
     * @throws \Exception
     * @return array
     */
    public function findList(DbConnection $conn = null) {
    	$conn = $this->getConnection($conn);
    	$dbName = Config::get(Config::DB_NAME);
    	$query = $this->select->toString($dbName);
    	$primaryResults = $conn->execute($query);
    		
    	if($primaryResults === null) return null;
    		
    	$rowCount = count($primaryResults);
    	if($rowCount == 0) return array();
    		
    	return $this->processResult($primaryResults, $conn, $dbName);
    }
    
    /**
     * 
     * @param DbConnection $conn
     * @return Entity
     */
    public function findFirst(DbConnection $conn = null) : Entity {
    	$conn = $this->getConnection($conn);
    	$dbName = Config::get(Config::DB_NAME);
    	$this->select->setLimit(1);
    	$query = $this->select->toString($dbName);
    	$primaryResults = $conn->execute($query);
    	
    	if($primaryResults == null) return null;
    	
    	$rowCount = count($primaryResults);
    	if($rowCount == 0) return null;
    	
    	$entities = $this->processResult($primaryResults, $conn, $dbName);
    	return $entities[0];
    }
    
    /**
     * 
     * @param DbConnection $conn
     * 
     * @throws SqlException
     * @return Entity|null
     */
    public function findUnique($conn = null) {
    	$conn = $this->getConnection($conn);
    	$dbName = Config::get(Config::DB_NAME);
    	$query = $this->select->toString($dbName);
    	$primaryResults = $conn->execute($query);
    	
    	if($primaryResults === null) return null;
    	
    	$rowCount = count($primaryResults);
    	if($rowCount == 0) return null;
    		
    	$entities = $this->processResult($primaryResults, $conn, $dbName);
    	
    	if(count($entities) != 1)
    		throw new SqlException($query, new \Exception("this query return more than one entity"));
    	
    	return $entities[0];
    }
    
    protected function getConnection(DbConnection $conn = null) : DbConnection {
    	if ($conn == null) return DbConnection::getInstance();
    	return $conn;
    }
    
    /**
     * 
     * @param array $rows
     * @param DbConnection $conn
     * 
     * @return Entity[]
     */
    private function processResult(array $rows, DbConnection $conn, string $dbName) : array {
    	
    	if(count($rows) == 0) return array();
    	
    	$entities = array();
    	foreach ($rows as $row) $entities[] = $this->buildEntity($row);

    	if(count($this->extras) > 0) $this->processExtra($entities, $conn, $dbName);
    	
    	return $entities;
    }
    
    /**
     * 
     * @param Entity[] $entities
     * @param Select $select
     * @param DbConnection $conn
     */
    private function processExtra(array &$entities, DbConnection $conn, string $dbName) {
    	$discriminants = $this->entityClass::discriminantFields();
    	$discriminantValues = array();
	    
    	// Find discriminant values of entities
	    foreach ($discriminants as $discriminant) {
	    	$discriminantField = $this->select->findField($discriminant->name)->field->name;
	    	$fieldName = $discriminantField;
	    	$discriminantValues[$discriminantField] = array();
    		foreach ($entities as $entity) {
    			$discriminantValues[$discriminantField][] = $entity->$fieldName;
	    	}
    	}
    	
    	// Execute and load all extra queries
    	$lazyEntities = array();
    	foreach ($this->extras as $flatPath => $fetcher) {
    		
    		// Create conditions with entities's discriminant values
    		$listField = $this->entityClass::findField($flatPath);
    		if($listField instanceof ManyToMany) {
    			$linkFields = $listField->primaryFields;
    			$table = $fetcher->findTable(md5($listField->associativeTable->getName()));
    		} else {
    			$linkFields = $listField->linkFields;
    			$table = $fetcher->findTable("");
    		}
    		$linkFields = $fetcher->select->findFields(
    			array_map(function($linkField) { return $linkField->field; }, $linkFields), 
    			$table
    		);
    		$extraCondition = $fetcher->findConditions();
    		foreach ($discriminants as $discriminant) {
    			foreach ($linkFields as $linkField) {
    				if($linkField->field->name == $discriminant->name) {
    					$extraCondition->in($linkField, $discriminantValues[$discriminant->name]);
    					break;
    				}
    			}
    		}
    		
    		// Fetch and load the list
    		$fetcher->findExtraList($listField, $entities, $linkFields, $conn, $dbName, $dbName);
    	}
    }
    
    private function findExtraList(ExtraAssociation $field, array &$parentEntities, array $discriminants, DbConnection $conn, string $dbName) {
    	$query = $this->select->toString($dbName);
    	$primaryResults = $conn->execute($query);
    	
    	$rowCount = count($primaryResults);
    	if($rowCount != 0)
    		$this->processExtraResult($field, $parentEntities, $discriminants, $primaryResults, $conn, $dbName);
    }
    
    /**
     * @param ExtraAssociation $field
     * @param Entity[] $parentEntities
     * @param AliasedQueryField[] $discriminants
     * @param array $rows
     * @param DbConnection $conn
     */
    private function processExtraResult(ExtraAssociation $field, array &$parentEntities, array $discriminants, array $rows, DbConnection $conn, string $dbName) {
    	
    	if(count($rows) == 0) return array();
    	
    	$fieldName = $field->name;
    	$entities = array();
    	foreach ($rows as $row) {
    		$parentEntity = null;
    		foreach ($parentEntities as $entity) {
    			$itIsIt = true;
    			foreach ($discriminants as $discriminant) {
    				$discriminantName = $discriminant->field->name;
    				if($entity->$discriminantName != $row[$discriminant->getAlias()]) {
    					$itIsIt = false;
    					break;
    				}
    			}
    			if($itIsIt)	$parentEntity = $entity;
    		}
    		
    		$entity = $this->buildEntity($row);
    		
    		if(!isset($parentEntity->$fieldName)) $parentEntity->$fieldName = array();
    		$parentEntity->$fieldName[] = $entity;
    		$entities[] = $entity;
    	}
    		
    	if(count($this->extras) > 0) $this->processExtra($entities, $conn, $dbName);
    }
    
    /**
     * @param array $row
     * @param Entity $entityClass
     * @param Select $select
     * @return Entity
     */
    private function buildEntity(array $row) : Entity {
    	$transformedRow = array();
    	foreach ($this->select->getFields() as $field) {
    		$this->setStepValue(explode("-", $field->getAlias()), $field, $transformedRow, $row);
    	}
    	return new $this->entityClass($transformedRow);
    }
    
    /**
     * Permet de construire le tableau associatif permettant de charger les models via la fonction Entity.fill(array $data)
     * 
     * @param array[] $path
     * @param QueryField $field
     * @param array $data
     * @param array $row
     * @param int $stepIndex
     */
    private function setStepValue(array $path, QueryField $queryField, array &$data, $row, int $stepIndex = 0) {
    	$finalStep = $stepIndex == (count($path) - 1);
    	$step = $path[$stepIndex];
    	if ($finalStep) {
   			$data[$step] = $this->getSqlValueForEntity($queryField->field, $row[$queryField->getAlias()]);
    	} else {
	    	if (!array_key_exists($step, $data))
	    		$data[$step] = array();
	    	
	    	$this::setStepValue($path, $queryField, $data[$step], $row, $stepIndex + 1);
    	}
    }
    
    /**
     * Permet de convertir une donnée de la bdd en une donnée utilisable par PHP
     *
     * @param Field $field
     * @param string $sqlValue
     * @throws \Exception
     * @return null|\DateTime|boolean|string|integer
     */
    private function getSqlValueForEntity(Field $field, string $sqlValue=null) {
    	if($sqlValue == null)
    		return null;
    	
    	switch ($field->dataType) {
    		case Field::DATE : return new \DateTime($sqlValue);
    		case Field::DATE_TIME : return new \DateTime($sqlValue);
    		case Field::BOOLEAN : return $sqlValue == 0 ? false : true;
    		case Field::FLOAT : return floatval($sqlValue);
    		case Field::INT : return intval($sqlValue);
    		case Field::TEXT : return $sqlValue;
    		default: throw new \Exception("Type inconnu: " . $field->dataType);
    	}
    }
    
    /**
     * Get flat path from uncertain path ["field1->field2" | array("field1", "field2")]
     * 
     * @param string|array $path
     */
    private function getFlatPath($path){
    	if(is_string($path)) {
    		return $path; 
    	} else if(is_array($path)) {
    		return implode("-", $pieces);
    	} else {
    		throw new IllegalArgumentException("path", "must be a string or an array");
    	}
    }
    
    /**
     * Get select from path
     * 
     * @param string $path
     * @return Fetcher|null
     */
    private function getFetcher(string $flatPath) {
    	if ($flatPath == "")
    		return $this;
    	else
    		return $this->searchFetcher($flatPath);
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
    	foreach ($fields as $field)
    		$result[] = new QueryField($table, $field);
    	return $result;
    }
    
    /**
     * Search the corresponding select path in secondaries select. If none found null return.
     * 
     * @param string $path
     * @return array(string, Fetcher)|null
     */
    private function searchFetcher(string $path) {
    	if($path == "") {
    		return null;
    	}
    	foreach ($this->extras as $extraPath => $fetcher) {
    		if($extraPath == $path) {
    			return array($extraPath, $fetcher);
    		} else {
    			$explodePath = explode("-", $path);
    			$nextPath = implode("-", array_slice($explodePath, 1, count($explodePath)));
    			return $fetcher->searchFetcher($nextPath);
    		}
    	}
    }
}