<?php

namespace framework\ui;

class Input extends Control {

	public function __construct(string $type, string $name) {
		parent::__construct($name);
		$this->setType($type);
	}

	/**
	 *
	 * @param string $height
	 * @return Input
	 */
	public function setHeight(string $height) {
		$this->putArgument("height", $height);
		return $this;
	}

	/**
	 *
	 * @param string $type
	 * @return Input
	 */
	public function setType(string $type) {
		$this->putArgument("type", $type);
		return $this;
	}

	/**
	 *
	 * @param string|null $value
	 * @return Input
	 */
	public function setValue($value) {
		if($value != null) {
			$this->putArgument("value", $value);
		}
		return $this;
	}

	/**
	 *
	 * @param string $placeholder
	 * @return Input
	 */
	public function setPlaceholder(string $placeholder) {
		$this->putArgument("placeholder", $placeholder);
		return $this;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see HtmlElement::render()
	 */
	public function render() {
		echo "<input " . $this->buildArguments() . " />";
	}
}

