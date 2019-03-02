<?php
namespace framework\sql\core\query;

use framework\sql\core\metadata\Table;

/**
 * Represent a SQL DELETE instructions which is used to delete entity's data from database
 */
class Delete {

	/**
	 * Entity table
	 *
	 * @var Table
	 */
	private $table;

	/**
	 *
	 * @var ConditionExpression
	 */
	public $conditions;

	/**
	 *
	 * @param Table $table
	 * @param array $fieldValueMap
	 */
	function __construct(Table $table) {
		$this->table = $table;
		$this->conditions = new ConditionExpression();
	}

	/**
	 * Build SQL query
	 *
	 * @return string
	 */
	public function toString(string $dbName) {
		$sql = "DELETE FROM `" . $dbName . "`.`" . $this->table->getName() . "`";
		$sqlConditions = $this->conditions->toString();
		if ($sqlConditions == "")
			return $sql;
		else
			return $sql . " WHERE " . $sqlConditions;
	}
}