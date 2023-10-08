<?php
namespace phpessential\mvc\sql\core\query;

use phpessential\mvc\exception\IllegalAccessException;
use phpessential\mvc\sql\utils\SqlUtils;

class ConditionExpression {
	private $sqlString = "";
	private $isDisjonction = false;

	/**
	 *
	 * @param QueryField $field
	 * @param string $operation
	 * @param string|QueryField $condition
	 */
	public function operation(QueryField $field, string $operation, $condition): ConditionExpression {
		$this->sqlString .= $this->getLogicalOperator() . $field->getAccessString() . " " . $operation . " ";
		
		if ($condition instanceof QueryField) {
			$this->sqlString .= $condition->getAccessString() . " ";
		} else {
			$this->sqlString .= $this->ensureValue($field, $condition) . " ";
		}
		return $this;
	}

	/**
	 *
	 * @param AliasedQueryField $field
	 * @param string $operation
	 * @param string $condition
	 */
	public function like(QueryField $field, $condition): ConditionExpression {
		$this->sqlString .= $this->getLogicalOperator() . $field->getAccessString() . " LIKE " . $this->ensureValue($field, $condition, true) . " ";
		return $this;
	}

	/**
	 *
	 * @param QueryField $field
	 * @param \DateTime $start
	 * @param \DateTime $end
	 */
	public function between(QueryField $field, \DateTime $start, \DateTime $end): ConditionExpression {
		$this->sqlString .= $this->getLogicalOperator() .
				"(" .
				$field->getAccessString() .
				" BETWEEN " .
				$this->ensureSqlValue($field, $start) .
				" AND " .
				$this->ensureSqlValue($field, $end) .
				") ";
		return $this;
	}

	public function eq(QueryField $field, $condition): ConditionExpression {
		return $this->operation($field, "=", $condition);
	}

	public function ne(QueryField $field, $condition): ConditionExpression {
		return $this->operation($field, "<>", $condition);
	}

	public function lt(QueryField $field, $condition): ConditionExpression {
		return $this->operation($field, "<", $condition);
	}

	public function gt(QueryField $field, $condition): ConditionExpression {
		return $this->operation($field, ">", $condition);
	}

	public function ge(QueryField $field, $condition): ConditionExpression {
		return $this->operation($field, ">=", $condition);
	}

	public function le(QueryField $field, $condition): ConditionExpression {
		return $this->operation($field, "<=", $condition);
	}

	public function in(QueryField $field, array $values): ConditionExpression {
		if (count($values) == 0) {
			throw new IllegalAccessException("values", "empty values not allowed in SQL IN clause !");
		}
		$strValues = implode(",", $values);
		$this->sqlString .= $this->getLogicalOperator() . $field->getAccessString() . " IN(" . $strValues . ") ";
		return $this;
	}

	public function isNull(QueryField $field) {
		$this->sqlString .= $this->getLogicalOperator() . $field->getAccessString() . " IS NULL ";
	}

	public function isNotNull(QueryField $field) {
		$this->sqlString .= $this->getLogicalOperator() . $field->getAccessString() . " IS NOT NULL ";
	}

	public function disjonction(): ConditionExpression {
		$this->sqlString .= $this->getLogicalOperator() . " (";
		$this->isDisjonction = true;
		return $this;
	}

	public function endJonction(): ConditionExpression {
		$this->isDisjonction = false;
		$this->sqlString .= ") ";
		return $this;
	}

	public function aditionnalCondition(ConditionExpression $conditions) {
		$this->sqlString .= $this->getLogicalOperator() . " " . $conditions->toString();
	}

	public function toString(): string {
		return $this->sqlString;
	}

	private function getLogicalOperator(): string {
		if ($this->sqlString == "" || substr($this->sqlString, - 1) == "(") {
			return "";
		} else {
			return ($this->isDisjonction) ? "OR " : "AND ";
		}
	}

	private function ensureValue(QueryField $field, $value, bool $forLike = false) {
		$ensuredValue = SqlUtils::ensureSqlValue($field->field, $value);
		if ($forLike) {
			$ensuredValue = "%" . $ensuredValue . "%";
		}
		return SqlUtils::addQuote($field->field, $ensuredValue);
	}
}