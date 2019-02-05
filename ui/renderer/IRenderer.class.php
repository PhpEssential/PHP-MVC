<?php
namespace framework\ui\renderer;

use framework\ui\HtmlElement;

/**
 * This interface allow you to realize custom draw of user controls
 *
 */
interface IRenderer {

	/**
	 *
	 * @param HtmlElement $element
	 */
	function render(HtmlElement $element);
}