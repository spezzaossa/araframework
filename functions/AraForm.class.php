<?php

class AraForm
{
	const CHECK_PASS = 1;
	const CHECK_WARNING = 2;
	const CHECK_FAIL = -1;

	private $form = NULL;
	private $error = NULL;

	public function __construct()
	{
		$this->form = array();
	}

	public function addField(AraFormField $field)
	{
		$this->form[$field->name()] = $field;
	}

	public function checkParams($params)
	{
		$this->error = '';
		$success = self::CHECK_PASS;
		foreach($this->form as $field)
		{
			$param = isset($params[$field->name()]) ? $params[$field->name()] : null;
			$check = $field->check($param);
			switch($check)
			{
				case AraFormField::CHECK_FAIL:
				{
					if ($success == self::CHECK_PASS) $success = self::CHECK_WARNING;
					$this->error .= 'Compila correttamente il campo '.$field->label().'.<br/>';
					break;
				}
				case AraFormField::CHECK_MISSING:
				{
					$success = self::CHECK_FAIL;
					$this->error .= 'Il campo '.$field->label().' &egrave; richiesto.<br/>';
					break;
				}
			}
		}
		return $success;
	}

	public function getError()
	{
		return $this->error;
	}

	public function getField($name)
	{
		return $this->form[$name];
	}

	public function emptyFields()
	{
		foreach($this->form as $field)
			$field->emptyValue();
	}

	public function changeStyle($style)
	{
		foreach($this->form as $field)
			$field->style($style);
	}
}

?>