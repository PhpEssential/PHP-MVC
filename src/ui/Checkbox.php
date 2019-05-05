<?php

namespace phpessential\mvc\ui;

class Checkbox extends Input {

    public function __construct(string $name) {
        parent::__construct("checkbox", $name, "custom-control-input");
    }

    /**
     *
     * @param bool $checked
     * @return Checkbox
     */
    public function setChecked(bool $checked) {
        if ($checked) {
            $this->putArgument("checked");
        } else {
            $this->removeArgument("checked");
        }
        return $this;
    }

}
