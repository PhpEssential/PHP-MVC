<?php

namespace framework\ui;

abstract class Control extends HtmlElement {

	/**
	 *
	 * @param string $type
	 * @param string $name
	 */
	public function __construct(string $name) {
		$this->setName($name)
			->setId($name);
	}

	/**
	 *
	 * @param bool $disabled
	 * @return Control
	 */
	public function setDisabled(bool $disabled) {
		if ($disabled) {
			$this->putArgument("disabled");
		} else {
			unset($this->arguments["disabled"]);
		}
		return $this;
	}
}

