<?php
namespace framework\ui;

class TextArea extends Control {
	
	/**
	 * 
	 * @var string
	 */
	private $value;
	
	public function __construct(string $name) {
		parent::__construct($name);
	}
	
	/**
	 *
	 * @param int $cols
	 * @return TextArea
	 */
	public function setCols(int $cols) {
		return $this->putArgument("cols", strval($rows));
	}
	
	/**
	 *
	 * @param int $rows
	 * @return TextArea
	 */
	public function setRows(int $rows) {
		return $this->putArgument("rows", strval($rows));
	}

	/**
	 * 
	 * @param int $rows
	 * @return TextArea
	 */
	public function setRows(int $rows) {
		return $this->putArgument("rows", strval($rows));
	}
	
	/**
	 *
	 * @param string $value
	 * @return TextArea
	 */
	public function setValue(string $value) {
		$this->value = $value;
		return $this;
	}
	
	/**
	 *
	 * @param string $placeholder
	 * @return TextArea
	 */
	public function setPlaceholder(string $placeholder) {
		$this->putArgument("placeholder", $placeholder);
		return $this;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see HtmlElement::render()
	 */
	public function render() {
		echo "<textarea " . $this->buildArguments() . " >" . $this->value . "</textarea>";
	}
}