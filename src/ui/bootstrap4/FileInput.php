<?php
namespace phpessential\mvc\ui\bootstrap4;

use phpessential\mvc\ui\FileInput as DefaultFileInput;

class FileInput extends DefaultFileInput {

	public function __construct(string $name) {
		parent::__construct($name);
		$this->addClass("form-control-file");
	}
}