<?php

namespace phpessential\mvc\ui\bootstrap4;

use phpessential\mvc\ui\Container;
use phpessential\mvc\ui\Checkbox;
use phpessential\mvc\ui\Label;

class CustomCheckbox extends Container {
    /**
     *
     * @var Label
     */
    private $label;

    /**
     *
     * @var Checkbox
     */
    private $checkbox;

    public function __construct(string $name, $labelText = null) {
        parent::__construct("div");

        $this->checkbox = new Checkbox($name);
        $this->checkbox->addClass("custom-control-input");

        $this->label = (new Label())->addClass("custom-control-label");
        $this->label->setFor($this->checkbox->getId());
        if ($labelText != null) {
            $this->label->setText($labelText);
        }

        $this->addChild($this->checkbox)->addChild($this->label)->addClass("custom-control custom-checkbox");
    }

    public function setDisabled(bool $disabled): CustomCheckbox {
        $this->radio->setDisabled($disabled);
        return $this;
    }

    public function setChecked(bool $checked): CustomCheckbox {
        $this->checkbox->setChecked($checked);
        return $this;
    }

    public function addInputClass(string $class) {
        $this->checkbox->addClass($class);
    }

    public function putInputArgument(string $name, string $value = null) {
        $this->checkbox->putArgument($name, $value);
    }

    /**
     *
     * @param string $class
     * @return HtmlElement
     */
    public function addLabelClass(string $class) {
        $this->label->addClass($class);
        return $this;
    }

    /**
     *
     * @param string $name
     * @param string $value
     * @return HtmlElement
     */
    public function addLabelStyle(string $name, string $value) {
        $this->label->addStyle($name, $value);
        return $this;
    }

}
