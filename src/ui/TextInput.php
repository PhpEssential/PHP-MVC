<?php
namespace tse\mvc\ui;

class TextInput extends Input {

	public function __construct(string $name) {
		parent::__construct("text", $name);
	}
}