<?php
namespace tse\mvc\ui;

class RangeInput extends Input {

	public function __construct(string $name) {
		parent::__construct(self::TYPE_RANGE, $name, "custom-range");
	}

	/**
	 *
	 * @param string $step
	 * @return RangeInput
	 */
	public function setMin(int $min) {
		$this->putArgument("min", $min);
		return $this;
	}

	/**
	 *
	 * @param string $step
	 * @return RangeInput
	 */
	public function setMax(int $max) {
		$this->arguments ["max"] = $max;
		return $this;
	}
}