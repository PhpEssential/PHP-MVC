<?php
namespace tse\mvc\ui\bootstrap4;

use tse\mvc\ui\NumberInput as DefaultNumberInput;

class NumberInput extends DefaultNumberInput {

	public function __construct(string $name) {
		parent::__construct($name);
		$this->addClass("form-control");
	}
}