<?php
namespace framework\ui;

class Label extends HtmlElement {
	private $text = "";

	/**
	 *
	 * @param string $for
	 * @return Label
	 */
	public function setFor(string $for) {
		$this->putArgument("for", $for);
		return $this;
	}

	/**
	 *
	 * @param string $text
	 * @return Label
	 */
	public function setText(string $text) {
		$this->text = $text;
		return $this;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see HtmlElement::render()
	 */
	public function render() {
		echo "<label " . $this->buildArguments() . ">" . $this->text . "</label>";
	}
}