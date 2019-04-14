<?php
namespace phpessential\mvc\ui\bootstrap4;

use phpessential\mvc\ui\Select as DefaultSelect;

class Select extends DefaultSelect {

	public function __construct(string $name, array $options = array()) {
		parent::__construct($name, $options);
		$this->addClass("form-control");
	}
}