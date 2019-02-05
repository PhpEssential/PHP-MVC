<?php
namespace framework\ui\bootstrap4;

use framework\ui\TextArea as DefaultTextArea;

class TextArea extends DefaultTextArea {
	
	public function __construct(string $name) {
		parent::__construct($name);
		$this->addClass("form-control");
	}
}