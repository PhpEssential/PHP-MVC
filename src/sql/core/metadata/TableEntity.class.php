<?php
namespace phpessential\mvc\sql\core\metadata;

use phpessential\mvc\sql\models\Entity;

/**
 * Table link to an Entity class
 */
class TableEntity extends Table {
	
	/**
	 * Entity class
	 *
	 * @var Entity
	 */
	private $entity;

	public function __construct(string $sqlName, string $entity) {
		parent::__construct($sqlName);
		$this->entity = $entity;
	}

	/**
	 *
	 * @return Entity
	 */
	public function getEntity() {
		return $this->entity;
	}
}