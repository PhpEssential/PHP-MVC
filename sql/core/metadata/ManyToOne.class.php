<?php
namespace framework\sql\core\metadata;

use framework\sql\models\Entity;

/**
 * Represent a many to one association
 */
class ManyToOne extends DirectAssociation {

	/**
	 *
	 * @param string $name
	 * @param Entity $foreignClass
	 * @param array $fieldMap
	 * @param int $dir
	 */
	function __construct(string $name, $foreignClass, array $fieldMapping) {
		parent::__construct($name, $foreignClass, self::createLinkFields($fieldMapping));
	}
}