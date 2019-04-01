<?php
namespace tse\mvc\ui\bootstrap4;

use tse\mvc\ui\Select as DefaultSelect;

class Select extends DefaultSelect {

	public function __construct(string $name, array $options = array()) {
		parent::__construct($name, $options);
		$this->addClass("form-control");
	}
}