<?php

namespace framework\ui\bootstrap4;

use framework\ui\PasswordInput as DefaultPasswordInput;

class PasswordInput extends DefaultPasswordInput {

	public function __construct(string $name) {
		parent::__construct($name);
		$this->addClass("form-control");
	}
}

