<?php
namespace framework\sql\core\metadata;

use framework\sql\models\Entity;

/**
 * Represent an association which not need to execute extra query during the fetch
 */
class DirectAssociation extends AbstractAssociation {

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
		parent::__construct($name, $foreignClass, $foreignFields);
	}
}