<?php
namespace tse\mvc\ui;

class Checkbox extends Input {

	public function __construct(string $name) {
		parent::__construct("checkbox", $name, "custom-control-input");
	}

	/**
	 *
	 * @param bool $checked
	 * @return Checkbox
	 */
	public function setChecked(bool $checked) {
		$this->putArgument("checked");
		return $this;
	}
}