<?php

namespace framework\ui;

class Radio extends Input {
	
	public function __construct(string $name) {
		parent::__construct("radio", $name, "custom-control-input");
	}
	
	public function setChecked(bool $checked) : Checkbox {
		$this->putArgument("checked");
		return $this;
	}
}

