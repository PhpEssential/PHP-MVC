<?php
namespace framework\sql\core\query;

use framework\sql\core\metadata\Field;

class AggregateQueryField extends AliasedQueryField {

	
	/**
	 * Aggregate function name
	 *
	 * @var string
	 */
	private $function;

	/**
	 * 
	 * @param string $function
	 * @param AliasedTable $table
	 * @param Field $field
	 * @param string $alias
	 * @param string $path
	 */
	function __construct(string $function, AliasedTable $table, Field $field, string $alias, string $path) {
		parent::__construct($table, $field, $alias, $path);
		$this->function = $function;
	}

	public function getFunction(): string {
		return $this->function;
	}

	public function getAccessString(): string {
		return '"' . $this->getAlias() . '"';
	}

	public function toString() {
		return $this->function . "(" . parent::getAccessString() . ") " . $this->getAccessString();
	}
}