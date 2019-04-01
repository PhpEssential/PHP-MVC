<?php
namespace tse\mvc\ui\renderer;

use tse\mvc\ui\HtmlElement;
use tse\mvc\ui\Container;

/**
 * Basic renderer
 */
class DefaultRenderer implements IRenderer {

	/**
	 * User control container
	 *
	 * @var Container
	 */
	private $container;

	public function __construct() {
		$this->container = new Container("div");
	}

	/**
	 * Add class to user control container
	 *
	 * @param string $class
	 */
	public function addContainerClass(string $class) {
		$this->container->addClass($class);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see IRenderer::render()
	 */
	public function render(HtmlElement $element) {
		$this->container->setId($element->getId() . "_container")->addChild($element)->render();
	}
}