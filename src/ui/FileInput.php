<?php

namespace phpessential\mvc\ui;

class FileInput extends Input {

    public function __construct(string $name) {
        parent::__construct("file", $name);
    }

}
