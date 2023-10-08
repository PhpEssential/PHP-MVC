<?php
namespace phpessential\mvc\sql\core\query;

/**
 * Représentation d'une jointure SQL
 */
class Join {

	/**
	 * Type de la jointure
	 *
	 * @var string
	 */
	public $joinType;

	/**
	 * Table avec alias
	 *
	 * @var AliasedTable
	 */
	public $table;

	/**
	 * Condition de jointure
	 *
	 * @var ConditionExpression
	 */
	public $conditions;

	function __construct(string $joinType, AliasedTable $table, ConditionExpression $conditions) {
		$this->joinType = $joinType;
		$this->table = $table;
		$this->conditions = $conditions;
	}

	/**
	 * Construit la chaine SQL
	 *
	 * @return string
	 */
	public function toString(string $dbName): string {
		return $this->joinType . " `" . $dbName . "`.`" . $this->table->getTable()->getName() . "` " . $this->table->getAlias() . " ON " . $this->conditions->toString();
	}
}