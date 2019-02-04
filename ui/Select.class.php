<?php

namespace framework\ui;

class Select extends Control {
	
	private $selectedValues = array();
	private $options;
	
	public function __construct(string $name, array $options = array()) {
		parent::__construct($name);
		
		$this->options = $options;
	}
	
	/**
	 * @param $value string
	 * 
	 * @return Select
	 */
	public function addSelectedValue(string $value) : Select {
		$this->selectedValues[] = $value;
		return $this;
	}
	
	/**
	 * 
	 * @param array $values
	 * @return Select
	 */
	public function addSelectedValues(array $values) : Select {
		$this->selectedValues = array_merge($this->selectedValues, $values);
		return $this;
	}
	
	public function setMultiple(bool $multiple) : Select {
		$this->putArgument("multiple");
		return $this;
	}
	
	public function setSize(int $size) : Select {
		$this->putArgument("size", $size);
		return $this;
	}
	
	public function render() {
		echo "<select " . $this->buildArguments() . " >" . $this->buildOptions() . "</select>";
	}
	
	private function buildOptions() : string {
		$htmlOptions = "";
		foreach ($this->options as $value => $label) {
			$htmlOptions .= "<option value=\"$value\" ";
			if(in_array($value, $this->selectedValues)) {
				$htmlOptions .= "selected ";
			}
			$htmlOptions .= ">$label</option>";
		}
		return $htmlOptions;
	}
}
?>