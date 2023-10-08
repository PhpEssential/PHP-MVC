<?php
namespace phpessential\mvc\sql\core\metadata;

use phpessential\mvc\sql\models\Entity;

/**
 * Represent a one to many association
 */
class OneToMany extends ExtraAssociation {
	public $referenceField;

	/**
	 *
	 * @param string $name
	 * @param Entity $foreignClass
	 * @param ManyToOne $referenceField
	 */
	function __construct(string $name, $foreignClass, ManyToOne $referenceField) {
		parent::__construct($name, $foreignClass, $referenceField->linkFields);
		$this->referenceField = $referenceField;
	}
}