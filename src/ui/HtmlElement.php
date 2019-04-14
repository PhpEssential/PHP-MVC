<?php
namespace phpessential\mvc\ui;

use phpessential\mvc\ui\renderer\IRenderer;
use phpessential\mvc\ui\renderer\DefaultRenderer;

abstract class HtmlElement {

	/**
	 *
	 * @var IRenderer
	 */
	private $renderer = null;

	/**
	 *
	 * @var array[string, string]
	 */
	private $style = array ();

	/**
	 *
	 * @var array[string, string]
	 */
	private $arguments = array ();

	/**
	 *
	 * @var string[]
	 */
	private $classes = array ();

	/**
	 *
	 * @return string|null
	 */
	public function getId() {
		return $this->getArgument("id");
		;
	}

	/**
	 *
	 * @return string|null
	 */
	public function getName() {
		return $this->getArgument("name");
	}

	/**
	 *
	 * @param string $name
	 * @return HtmlElement
	 */
	public function setName(string $name) {
		$this->putArgument("name", $name);
		return $this;
	}

	/**
	 *
	 * @param string $id
	 * @return HtmlElement
	 */
	public function setId(string $id) {
		$this->putArgument("id", $id);
		return $this;
	}

	/**
	 *
	 * @param array $args
	 * @return HtmlElement
	 */
	public function putArgument(string $name, $value = null) {
		$this->arguments [$name] = strval($value);
		return $this;
	}

	/**
	 *
	 * @param array $arguments
	 * @return HtmlElement
	 */
	public function putArguments(array $arguments) {
		$this->arguments = $arguments + $this->arguments;
		return $this;
	}

	/**
	 *
	 * @param string $name
	 * @return string|NULL
	 */
	public function getArgument(string $name) {
		if (array_key_exists($name, $this->arguments)) {
			return $this->arguments [$name];
		}
		return null;
	}

	/**
	 *
	 * @return array[String, String]
	 */
	public function getArguments(): array {
		return $this->arguments;
	}

	/**
	 *
	 * @return array
	 */
	public function getClasses(): array {
		return $this->classes;
	}

	/**
	 *
	 * @param string $class
	 * @return HtmlElement
	 */
	public function addClass(string $class) {
		$this->classes [] = $class;
		return $this;
	}

	/**
	 *
	 * @param array $classes
	 * @return HtmlElement
	 */
	public function addClasses(array $classes) {
		$this->classes = $this->classes + $classes;
		return $this;
	}

	/**
	 *
	 * @param bool $visible
	 * @return HtmlElement
	 */
	public function setVisible(bool $visible) {
		if ($visible) {
			if ($this->getStyle("display") == "none") {
				$this->removeStyle("display");
			}
		} else {
			$this->addStyle("display", "none");
		}
		return $this;
	}

	/**
	 *
	 * @param array $styles
	 * @return HtmlElement
	 */
	public function addStyles(array $styles) {
		$this->style + $styles;
		return $this;
	}

	/**
	 *
	 * @param string $name
	 * @param string $value
	 * @return HtmlElement
	 */
	public function addStyle(string $name, string $value) {
		$this->style + array (
				$name => $value
		);
		return $this;
	}

	/**
	 *
	 * @param string $name
	 * @return string|NULL
	 */
	public function getStyle(string $name) {
		if (array_key_exists($name, $this->style)) {
			return $this->style [$name];
		}
		return null;
	}

	/**
	 *
	 * @param string $name
	 * @return HtmlElement
	 */
	public function removeStyle(string $name) {
		if (array_key_exists($name, $this->style)) {
			unset($name);
		}
		return $this;
	}

	/**
	 *
	 * @param IRenderer $renderer
	 * @return HtmlElement
	 */
	public function setRenderer(IRenderer $renderer) {
		$this->renderer = $renderer;
		return $this;
	}

	/**
	 */
	public function draw() {
		$this->renderer == null ? (new DefaultRenderer())->render($this) : $this->renderer->render($this);
	}

	/**
	 *
	 * @return string
	 */
	protected function buildArguments(): string {
		if (count($this->classes) > 0) {
			$this->putArgument("class", implode(" ", $this->classes));
		}
		if (count($this->style) > 0) {
			$this->putArgument("style", $this->prepareStyle());
		}

		$strArgs = "";
		foreach ( $this->arguments as $name => $value ) {
			$strArgs .= " " . $name;
			if ($value != null) {
				$strArgs .= "=\"$value\"";
			}
		}
		return $strArgs;
	}

	/**
	 *
	 * @return string
	 */
	private function prepareStyle(): string {
		$strStyle = "";
		foreach ( $this->style as $name => $value ) {
			$strStyle .= $name . ":" . $value . " ";
		}
		return substr($strStyle, 0, - 1);
	}

	/**
	 */
	public abstract function render();
}