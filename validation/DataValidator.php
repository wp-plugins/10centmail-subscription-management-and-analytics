<?php

include_once('validators/IValidator.php');
include_once('validators/impl/EmailValidator.php');
include_once('validators/impl/IPAddressValidator.php');
include_once('validators/impl/NoSpecialCharactersValidator.php');
include_once('validators/impl/NotEmptyValidator.php');
include_once('validators/impl/StringLengthValidator.php');

class DataValidator
{

	public $data;
	private $_valid;
	private $_invalidCount = 0;
	private $_validationErrorMessages = array();

	function __construct($mapper)
	{
		$this->data = $mapper;
	}

	function setValue($key, $value)
	{
		$this->data->$key->value = $value;
	}

	public function valid()
	{
		return $this->_valid;
	}

	public function getValidationErrorMessages()
	{
		return $this->_validationErrorMessages;
	}

	public function clearValidation()
	{
		$this->_validationErrorMessages = array();
		$this->_invalidCount = 0;
	}

	public function validate()
	{
		$this->clearValidation();
		foreach ($this->data as $prop => $set) {
			if (!empty($this->data->$prop->validators) &&
				count($this->data->$prop->validators) > 0
			) {
				foreach ($this->data->$prop->validators as $v) {
					$validator;
					if ($v == 'StringLengthValidator') {
						$validator = new $v($this->data->$prop->minmax[0], $this->data->$prop->minmax[1]);
					} else {
						$validator = new $v;
					}
					if (!$validator->valid($this->data->$prop->value)) {
						$this->_validationErrorMessages[] = $validator->error($this->data->$prop->value, $this->data->$prop->label);
						$this->_invalidCount++;
					}

				}
			}
		}
		$this->setValidState();


	}

	private function setValidState()
	{
		if ($this->_invalidCount == 0) {
			$this->_valid = true;
		} else {
			$this->setErrorMessages();
			$this->_valid = false;
		}
	}

	private function setErrorMessages()
	{
		$this->data->messages = array();
		foreach ($this->_validationErrorMessages as $message) {
			$this->data->messages[] = (object)array(
				"message" => $message
			);
		}
	}

}
