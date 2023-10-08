<?php

namespace phpessential\mvc\ui;

class TextInput extends Input {

    public function __construct(string $name) {
        parent::__construct("text", $name);
    }

}
