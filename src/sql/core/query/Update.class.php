<?php
namespace phpessential\mvc\sql\core\query;

use phpessential\mvc\sql\core\metadata\Table;
use phpessential\mvc\sql\utils\SqlUtils;
use phpessential\mvc\sql\core\metadata\Field;

/**
 * Represent a SQL UPDATE instructions which is used to update entity's data into database
 */
class Update {

	/**
	 * Entities fields
	 *
	 * @var array[Field, mixed]
	 */
	private $fieldValueMap = array ();

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
	function __construct(Table $table, array $fieldValueMap) {
		$this->table = $table;
		$this->fieldValueMap = $fieldValueMap;
		$this->conditions = new ConditionExpression();
	}

	/**
	 * Build SQL query
	 *
	 * @return string
	 */
	public function toString(string $dbName) {
		$sql = "UPDATE `" . $dbName . "`.`" . $this->table->getName() . "` SET ";
		foreach ( $this->fieldValueMap as $fieldName => $fieldValueEntry ) {
			$field = $fieldValueEntry [0];
			$value = $fieldValueEntry [1];
			$sql .= "`" . $fieldName . "`=" . SqlUtils::addQuote($field, SqlUtils::ensureSqlValue($field, $value)) . ",";
		}
		$sql = substr($sql, 0, - 1);

		$sqlConditions = $this->conditions->toString();
		if ($sqlConditions == "")
			return $sql;
		else
			return $sql . " WHERE " . $sqlConditions;
	}
}