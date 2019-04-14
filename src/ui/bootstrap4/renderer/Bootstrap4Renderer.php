<?php
namespace phpessential\mvc\ui\bootstrap4\renderer;

use phpessential\mvc\ui\Container;
use phpessential\mvc\ui\HtmlElement;
use phpessential\mvc\ui\Label;
use phpessential\mvc\ui\renderer\DefaultRenderer;

class Bootstrap4Renderer extends DefaultRenderer {

	/**
	 *
	 * @var Container
	 */
	private $container;

	/**
	 *
	 * @var Container
	 */
	private $elementContainer;

	/**
	 *
	 * @var Container
	 */
	private $labelContainer;

	/**
	 *
	 * @var Label
	 */
	private $label;

	/**
	 *
	 * @var bool
	 */
	private $hasLabel = false;

	public function __construct(string $label = null) {
		$this->container = (new Container("div"));
		$this->container->addClasses(array (
				"row","form-group"
		));

		$this->elementContainer = new Container("div");
		$this->labelContainer = new Container("div");
		$this->label = new Label();
		if ($label != null) {
			$this->hasLabel = true;
			$this->setLabelText($label . " : ");
		}

		$this->labelContainer->addChild($this->label);
	}

	/**
	 *
	 * @return Container
	 */
	public function getContainer(): Container {
		return $this->container;
	}

	/**
	 *
	 * @param string $class
	 * @return Bootstrap4Renderer
	 */
	public function addContainerClass(string $class) {
		$this->container->addClass($class);
		return $this;
	}

	/**
	 *
	 * @param string $text
	 * @return Bootstrap4Renderer
	 */
	public function setLabelText(string $text) {
		$this->label->setText($text);
		$this->hasLabel = true;
		return $this;
	}

	/**
	 *
	 * @param string $class
	 * @return Bootstrap4Renderer
	 */
	public function addLabelClass(string $class) {
		$this->label->addClass($class);
		return $this;
	}

	/**
	 *
	 * @return Container
	 */
	public function getLabelContainer(): Container {
		return $this->labelContainer;
	}

	/**
	 *
	 * @param string $class
	 * @return Bootstrap4Renderer
	 */
	public function addLabelContainerClass(string $class) {
		$this->labelContainer->addClass($class);
		return $this;
	}

	/**
	 *
	 * @param string $class
	 * @return Bootstrap4Renderer
	 */
	public function addElementContainerClass(string $class) {
		$this->elementContainer->addClass($class);
		return $this;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see DefaultRenderer::render()
	 */
	public function render(HtmlElement $element) {
		$this->container->setId("container_" . $element->getId());
		$this->elementContainer->addChild($element);
		if ($this->hasLabel) {
			$this->container->addChild($this->labelContainer);
		}
		$this->container->addChild($this->elementContainer)->render();
	}
}