<?php
namespace framework\ui\bootstrap4;

use framework\ui\Input as DefaultInput;

class Input extends DefaultInput {

	public function __construct(string $type, string $name) {
		parent::__construct($type, $name);
		$this->addClass("form-control");
	}
}