<?php
namespace framework\sql\core\metadata;

use framework\sql\models\Entity;

/**
 * Represent an association between two entity
 * 
 */
class AbstractAssociation extends AbstractField {
    
	/**
	 * Attribute model's class
	 *
	 * @var Entity
	 */
	public $foreignClass;
	
	/**
	 * Foreign key referenced fields
	 *
	 * @var LinkField[]
	 */
	public $linkFields;
	
	public $cascade = false;
	
	/**
	 * 
	 * @param string $name
	 * @param Entity $foreignClass
	 * @param LinkField[] $foreignFields
	 */
	function __construct(string $name, $foreignClass, array $foreignFields) {
    	parent::__construct($name);
    	$this->foreignClass = $foreignClass;
    	$this->linkFields = $foreignFields;
    }
    
    public function setCascade(bool $cascade) : AbstractAssociation {
    	$this->cascade = $cascade;
    	return $this;
    }
    
    /**
     * 
     * 
     * @param array $fieldMapping
     * @param Entity $entityClass
     * 
     * @return LinkField[]
     */
    protected static function createLinkFields(array $fieldMapping) : array {
    	$links = array();
    	foreach ($fieldMapping as $sqlName => $field) {
    		$links[] = new LinkField($field, $sqlName);
    	}
    	return $links;
    }
}