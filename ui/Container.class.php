<?php
namespace framework\ui;

class Container extends HtmlElement {
	const DIV = "div";
	const SPAN = "span";
	const LINK = "a";

	protected $type;
	
	/**
	 * 
	 * @var array[HtmlElement]
	 */
	protected $children = array();
	
	/**
	 * 
	 * @param string $type
	 * @param string $name
	 */
	public function __construct(string $type) {
		$this->type = $type;
	}
	
	public function addChild(HtmlElement $child) : Container {
		$this->children[] = $child;
		return $this;
	}
	
	public function render() {
		echo "<" . $this->type . " " . $this->buildArguments() . ">";
		foreach ($this->children as $child) {
			$child->render();
		}
		echo "</" . $this->type . ">";
	}
}