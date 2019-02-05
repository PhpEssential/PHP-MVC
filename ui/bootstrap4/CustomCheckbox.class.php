<?php
namespace framework\ui\bootstrap4;

use framework\ui\Container;
use framework\ui\Checkbox;
use framework\ui\Label;

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
		
		$this->addChild($this->checkbox)
		->addChild($this->label)
		->addClass("custom-control custom-checkbox");
	}
	
	public function setDisabled(bool $disabled) : CustomCheckbox {
		$this->radio->setDisabled($disabled);
		return $this;
	}
	
	public function setChecked(bool $checked): CustomCheckbox {
		$this->checkbox->setChecked($checked);
		return $this;
	}
}