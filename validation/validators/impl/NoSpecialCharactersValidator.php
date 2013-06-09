<?php

class NoSpecialCharactersValidator implements IValidator
{

	public function valid($value)
	{
		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $value)) return false;
		return true;
	}

	public function error($value, $label)
	{
		return $label . " cannot contain special characters";
	}

}
