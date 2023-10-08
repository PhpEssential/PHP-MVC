<?php
namespace phpessential\mvc\sql\core\metadata;

use phpessential\mvc\sql\models\Entity;

/**
 * Represent a foreign key
 */
class OneToOne extends DirectAssociation {

	/**
	 *
	 * @var OneToOneRef
	 */
	public $referenceField;

	/**
	 *
	 * @param string $name
	 * @param Entity $foreignClass
	 * @param array $foreignFieldName
	 */
	function __construct(string $name, $foreignClass, OneToOneRef $referenceField) {
		parent::__construct($name, $foreignClass, $referenceField->linkFields);
		$this->referenceField = $referenceField;
	}
}