<?php
namespace framework\ui\bootstrap4\renderer;

use framework\ui\Container;
use framework\ui\HtmlElement;
use framework\ui\Label;
use framework\ui\renderer\DefaultRenderer;

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

	public function __construct(string $label = null) {
		$this->container = (new Container("div"));
		$this->container->addClasses(array (
				"row","form-group"
		));

		$this->elementContainer = new Container("div");
		$this->labelContainer = new Container("div");
		$this->label = new Label();
		if ($label != null) {
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
	 * @return Label
	 */
	public function getLabel(): Label {
		return $this->label;
	}

	/**
	 *
	 * @param string $text
	 * @return Bootstrap4Renderer
	 */
	public function setLabelText(string $text) {
		$this->label->setText($text);
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
		$this->container->addChild($this->labelContainer)->addChild($this->elementContainer)->render();
	}
}