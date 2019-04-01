<?php
namespace tse\mvc\ui;

class Radio extends Input {

	public function __construct(string $name) {
		parent::__construct("radio", $name, "custom-control-input");
	}

	public function setChecked(bool $checked): Radio {
		$this->putArgument("checked");
		return $this;
	}
}