<?php

interface IValidator
{
	public function valid($value);

	public function error($value, $label);
}
