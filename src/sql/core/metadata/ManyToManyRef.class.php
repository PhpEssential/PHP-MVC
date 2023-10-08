<?php
namespace phpessential\mvc\sql\core\metadata;

use phpessential\mvc\sql\models\Entity;

/**
 * Represent a many to many association
 */
class ManyToManyRef extends ExtraAssociation {
	
	/**
	 * Associative table
	 *
	 * @var Table
	 */
	public $associativeTable;
	
	/**
	 * Primary reference fields
	 *
	 * @var LinkField[]
	 */
	public $primaryFields = array ();
	
	/**
	 * Insert / Upade / Delete associtions if true
	 *
	 * @var LinkField[]
	 */
	public $cascadeAssociation = true;

	/**
	 *
	 * @param string $name
	 *        	- Attribute's name
	 * @param Entity $primaryClass
	 *        	- Class of model
	 * @param Entity $foreignClass
	 *        	- Attribute model's class
	 * @param string $associativeTableName
	 *        	- Sql name of the associative table
	 * @param array $primaryFieldMap
	 *        	- Primary reference fields for link with the model
	 * @param array $foreignFieldMap
	 *        	- Foreign reference fields for link with the attribute
	 */
	function __construct(string $name, $foreignClass, ManyToMany $referenceField) {
		parent::__construct($name, $foreignClass, self::createLinkFields($foreignFields));
		$this->associativeTable = new Table($associationTableName);
		$this->primaryFields = $this->createLinkFields($primaryFields);
	}

	public function setCascadeAssciation(bool $cascade): ManyToMany {
		$this->cascadeAssociation = $cascade;
		return $this;
	}
}