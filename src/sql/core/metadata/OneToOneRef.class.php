<?php
namespace phpessential\mvc\sql\core\metadata;

use phpessential\mvc\sql\models\Entity;

/**
 * Represent a foreign key
 */
class OneToOneRef extends DirectAssociation {

	/**
	 *
	 * @param string $name
	 * @param Entity $foreignClass
	 * @param array $fieldMap
	 */
	function __construct(string $name, $foreignClass, array $fieldMap) {
		parent::__construct($name, $foreignClass, self::createLinkFields($fieldMap, $foreignClass));
	}
}