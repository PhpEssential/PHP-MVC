<?php

namespace phpessential\mvc\exception;

class IllegalArgumentException extends \Exception {

    public function __construct($argumentName = "", $message = "", $previous = null) {
        parent::__construct("Argument invalide: " . $argumentName . "(" . $message . ")", null, $previous);
    }

}
