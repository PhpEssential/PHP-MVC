<?php
namespace framework\sql\core\query;

/**
 * Représentation d'un champ bdd à trier
 */
class OrderedField {
	const ORDER_ASC = "ASC";
	const ORDER_DESC = "DESC";
	
	/**
	 * champ
	 *
	 * @var QueryField
	 */
	public $field;
	
	/**
	 * Sens du trie ASC | DESC
	 *
	 * @var string
	 */
	public $order;

	function __construct(QueryField $field, string $order) {
		$this->field = $field;
		$this->order = $order;
	}

	public function toString() {
		return $this->field->getAccessString() . " " . $this->order;
	}
}