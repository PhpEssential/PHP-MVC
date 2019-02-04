<?php

namespace framework\ui;

class FileInput extends Input {
	public function __construct(string $name) {
		parent::__construct("file", $name);
	}
}

