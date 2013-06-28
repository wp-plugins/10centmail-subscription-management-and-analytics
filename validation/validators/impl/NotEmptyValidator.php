<?php

class NotEmptyValidator implements IValidator
{

	public function valid($value)
	{
		$trimmedValue = trim($value);
		if (empty($trimmedValue)) return false;
		return true;
	}

	public function error($value, $label)
	{
		return $label . " cannot be an empty field";
	}

}
