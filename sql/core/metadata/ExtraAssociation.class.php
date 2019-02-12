<?php
namespace framework\sql\core\metadata;

use framework\sql\models\Entity;

/**
 * Represent an association which need to execute extra query during the fetch
 */
class ExtraAssociation extends AbstractAssociation {

	/**
	 *
	 * @param string $name
	 * @param Entity $foreignClass
	 * @param LinkField[] $foreignFields
	 */
	function __construct(string $name, $foreignClass, array $foreignFields) {
		parent::__construct($name, $foreignClass, $foreignFields);
	}
}