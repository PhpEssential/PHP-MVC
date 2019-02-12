<?php
namespace framework\sql\core\query;

use framework\sql\core\metadata\Field;

/**
 * Représentation d'un champ dans une requête SQL
 */
class QueryField {
	
	/**
	 *
	 * @var AliasedTable
	 */
	public $table;
	
	/**
	 *
	 * @var Field
	 */
	public $field;

	public function __construct(AliasedTable $table, Field $field) {
		$this->table = $table;
		$this->field = $field;
	}

	public function getAccessString(): string {
		return $this->table->getAlias() . "." . $this->field->sqlName;
	}
}