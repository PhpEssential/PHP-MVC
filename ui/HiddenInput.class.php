<?php
namespace framework\ui;

class HiddenInput extends Input {

	public function __construct(string $name) {
		parent::__construct("hidden", $name);
	}
}