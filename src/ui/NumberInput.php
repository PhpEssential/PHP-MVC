<?php
namespace tse\mvc\ui;

class NumberInput extends Input {

	public function __construct(string $name) {
		parent::__construct("number", $name);
	}

	/**
	 *
	 * @param string $step
	 * @return NumberInput
	 */
	public function setStep(string $step) {
		$this->putArgument("step", $step);
		return $this;
	}
}