<?php
namespace sql\core\query;

use phpessential\mvc\sql\models\Entity;

class DeleteFactory {
	
	/**
	 *
	 * @var Entity
	 */
	private $entity;

	/**
	 *
	 * @param Entity $entityClass
	 */
	public function __construct($entityClass) {
		$this->entity = $entityClass;
	}
}
