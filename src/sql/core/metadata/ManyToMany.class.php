<?php
namespace phpessential\mvc\sql\core\metadata;

use phpessential\mvc\sql\models\Entity;

/**
 * Represent a many to many association
 */
class ManyToMany extends ExtraAssociation {

	/**
	 * Insert, upade, delete associtions at same time as the primary entity
	 *
	 * @var bool
	 */
	public $cascadeAssociation = true;

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
	function __construct(string $name, $foreignClass, string $associationTableName, array $primaryFields, array $foreignFields) {
		parent::__construct($name, $foreignClass, self::createLinkFields($foreignFields));
		$this->associativeTable = new Table($associationTableName);
		$this->primaryFields = $this->createLinkFields($primaryFields);
	}

	/**
	 * Insert, upade, delete associtions at same time as the primary entity
	 *
	 * @param bool $cascade
	 * @return ManyToMany
	 */
	public function setCascadeAssciation(bool $cascade): ManyToMany {
		$this->cascadeAssociation = $cascade;
		return $this;
	}
}