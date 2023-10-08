<?php
namespace phpessential\mvc\sql\core\metadata;

use phpessential\mvc\sql\models\Entity;

/**
 * Represent an association between two entity
 */
class AbstractAssociation extends AbstractField {
	
	/**
	 * Foreign entity class
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
	
	/**
	 * Insert, update and delete the foreign entities at same time as the primary entity
	 *
	 * @var string
	 */
	public $cascade = false;

	/**
	 *
	 * @param string $name
	 *        	- Entity attribute name
	 * @param Entity $foreignClass
	 *        	- Foreign entity class
	 * @param LinkField[] $foreignFields
	 *        	- Foreign key reference fields
	 */
	function __construct(string $name, $foreignClass, array $foreignFields) {
		parent::__construct($name);
		$this->foreignClass = $foreignClass;
		$this->linkFields = $foreignFields;
	}

	/**
	 *
	 * @param bool $cascade
	 * @return AbstractAssociation
	 */
	public function setCascade(bool $cascade): AbstractAssociation {
		$this->cascade = $cascade;
		return $this;
	}

	/**
	 *
	 * @param array $fieldMapping
	 * @param Entity $entityClass
	 *
	 * @return LinkField[]
	 */
	protected static function createLinkFields(array $fieldMapping): array {
		$links = array ();
		foreach ( $fieldMapping as $sqlName => $field ) {
			$links [] = new LinkField($field, $sqlName);
		}
		return $links;
	}
}