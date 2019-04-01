<?php
namespace tse\mvc\ui\renderer;

use tse\mvc\ui\HtmlElement;

/**
 * This interface allow you to realize custom draw of user controls
 */
interface IRenderer {

	/**
	 *
	 * @param HtmlElement $element
	 */
	public function render(HtmlElement $element);
}