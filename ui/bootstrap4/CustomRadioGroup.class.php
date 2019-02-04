<?php

namespace framework\ui\bootstrap4;

use framework\ui\Control;
use framework\ui\HtmlElement;

class CustomRadioGroup extends Control {
	
	/**
	 * 
	 * @var array[string, CustomRadio]
	 */
	private $radios = array();
	
	public function __construct(string $name, array $groupValues = array()) {
		parent::__construct($name);
		
		foreach ($groupValues as $value => $label) {
			$this->radios[$value] = (new CustomRadio($name, $value, $label))
				->setId($name . "_" . $value);
		}
	}
	
	/**
	 * 
	 * @param string|null $value
	 * 
	 * @return CustomRadioGroup
	 */
	public function setSelectedValue($value) : CustomRadioGroup {
		if($value != null) $this->radios[$value]->setChecked(true);
		return $this;
	}
	
	public function setDisabled(bool $disable): Control {
		foreach ($this->radios as $value => $radio) {
			$radio->setDisable($disable);
		}
		return $this;
	}
	
	public function setVisible(bool $visible): Control {
		foreach ($this->radios as $value => $radio) {
			$radio->setVisible($visible);
		}
		return $this;
	}
	
	public function setInline(bool $inline) : CustomRadioGroup {
		foreach ($this->radios as $value => $radio) {
			$radio->setInline($inline);
		}
		return $this;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see HtmlElement::render()
	 */
	public function render() {
		foreach ($this->radios as $radioValue => $radio) {
			$radio->render();
		}
	}
}
?>