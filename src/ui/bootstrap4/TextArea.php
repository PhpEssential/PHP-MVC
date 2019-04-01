<?php
namespace tse\mvc\ui\bootstrap4;

use tse\mvc\ui\TextArea as DefaultTextArea;

class TextArea extends DefaultTextArea {

	public function __construct(string $name) {
		parent::__construct($name);
		$this->addClass("form-control");
	}
}