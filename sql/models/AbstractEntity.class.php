<?php
namespace framework\sql\models;

use framework\exception\IllegalAccessException;
use framework\exception\IllegalArgumentException;
use framework\sql\core\metadata\AbstractField;
use framework\sql\core\metadata\DirectAssociation;
use framework\sql\core\metadata\ExtraAssociation;
use framework\sql\core\metadata\Field;
use framework\sql\core\metadata\ManyToOne;

/**
 * Representation of datable row,
 */
class AbstractEntity {
    
	/**
	 * fields mapping cache 
	 * 
	 * @var AbstractField[]
	 */
    protected static $fields;

    function __construct(array $data = array()) {
    	if(count($data) > 0) {
        	$this->fill($data);
    	}
    }
    
    /**
     * Mapping between database fields and model attributes
     * 
     * @return AbstractField[]
     */
    protected static function fields() {
    	throw new IllegalAccessException("Unimplemented function: " . static::class . "->fields()");
    }
    
    /**
     * Get all non foreign fields of model
     *
     * @return Field[]
     */
    public static function getDirectFields() {
    	return static::getFields(function (AbstractField $field) {
    		return $field instanceof Field;
    	});
    }
    
    /**
     * Return all model fields
     *
     * @return Field[]
     */
    public static function getFields($filter = null) {
    	if(static::$fields == null) {
    		static::$fields = static::fields();
    	}
    	if($filter == null) {
    		return static::$fields;
    	}
    	return array_filter(static::$fields, $filter);
    }
    
       
    /**
     * Search field with path
     *
     * @param string|array $path
     * 
     * @return AbstractField
     */
    public static function findField($path) {
    	if(is_string($path)) {
    		$path = explode("-", $path);
    	}
    	$field = null;
    	$entityClass = static::class;
    	
    	// Decompose path into step
    	foreach ($path as $step) {
    		if($entityClass == null) {
    			throw new IllegalArgumentException("path", "problem with step: " . $step);
    		}
    		
    		// Search the step in model's fields
    		$fields = $entityClass::getFields();
    		foreach ($fields as $entityField) {
    			if($entityField->name == $step) {
    				$field = $entityField;
    				break;
    			}
    		}
    		
    		// If foreign field, re-assign the modelClass for the next step if this one is not the last 
    		if($field instanceof ManyToOne) {
    			$entityClass = $field->foreignClass;
    		} else {
    			// Throw an error if it's not the end of path
    			$entityClass = null; 
    		}
    	}
    	
    	return $field;
    }
    
    /**
     * Fonction utilitaire permettant de vérifier si la clé est présente dans le tableau de donnée provenant de la bdd
     *
     * @param string $key
     * @param array $data
     * @return null|\DateTime|boolean|string|integer
     */
    private function bindFieldFromQuery(AbstractField $field, array $data) {
    	$fieldName = $field->name;
    	$this->$fieldName = null;
    	
    	if(array_key_exists($fieldName, $data)) {
    		$this->$fieldName = $data[$fieldName];
    	}
    }
    
    /**
     * Fonction utilitaire permettant de vérifier si la clé est présente dans le tableau de donnée provenant de la bdd
     *
     * @param string $key
     * @param array $data
     * @return null|\DateTime|boolean|string|integer
     */
    private function bindEntityFromQuery(DirectAssociation $field, array $data) {
    	$fieldName = $field->name;
    	$this->$fieldName = null;
    	
    	if(array_key_exists($fieldName, $data)) {
    		$className = $field->foreignClass;
    		$this->$fieldName = new $className($data[$fieldName]);
    	}
    }
    
    /**
     * Fonction utilitaire permettant de vérifier si la clé est présente dans le tableau de donnée provenant de la bdd
     *
     * @param string $key
     * @param array $data
     * @return null|\DateTime|boolean|string|integer
     */
    private function bindListFromQuery(ExtraAssociation $field, array $data) {
    	$fieldName = $field->name;
    	$this->$fieldName = array();
    	
    	if(array_key_exists($fieldName, $data) && $data[$fieldName] != null) {
    		$className = $field->foreignClass;
    		foreach ($data[$fieldName] as $item) {
    			$this->$fieldName[] = new $className($item);
    		}
    	}
    }
    
    /**
     * Mapping between database query results and binding model values
     *
     * @param array $data
     * 					results from database query pre-transform:
     * 						- simple value: array[$entityAttr, $sqlValue]
     * 						- linked model: array[$entityAttr, array[$linkedEntityAttr, $linkedEntitySqlValue]]
     *
     */
    private function fill(array $data) {
    	$fields = $this->getFields();
    	foreach ($fields as $field) {
    		if($field instanceof DirectAssociation) {
    			$this->bindEntityFromQuery($field, $data);
    		} else if($field instanceof ExtraAssociation) {
    			$this->bindListFromQuery($field, $data);
    		} else {
    			$this->bindFieldFromQuery($field, $data);
    		}
    	}
    }
}