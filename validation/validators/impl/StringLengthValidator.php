<?php

class StringLengthValidator implements IValidator
{

	public $min;
	public $max;

	public function __CONSTRUCT($min, $max)
	{
		$this->min = $min;
		$this->max = $max;
	}

	public function valid($value)
	{
		if (strlen($value) >= $this->min && strlen($value) <= $this->max) return true;
		return false;
	}

	public function error($value, $label)
	{
		return $label . " must be " . $this->min . " to " . $this->max . " characters long";
	}

}
