<?php
namespace phpessential\mvc\ui\renderer;

use phpessential\mvc\ui\HtmlElement;

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