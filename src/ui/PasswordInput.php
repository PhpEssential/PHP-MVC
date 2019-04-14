<?php

namespace phpessential\mvc\ui;

class PasswordInput extends Input {

    public function __construct(string $name) {
        parent::__construct("password", $name);
    }

}
