<?php

namespace phpessential\mvc\ui\bootstrap4;

use phpessential\mvc\ui\TextInput as DefaultTextInput;

class TextInput extends DefaultTextInput {

    public function __construct(string $name) {
        parent::__construct($name);
        $this->addClass("form-control");
    }

}
