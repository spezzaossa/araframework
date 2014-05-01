<?php

class AraFormField
{
	const TYPE_TEXT = 1;
	const TYPE_EMAIL = 2;
	const TYPE_NUMBER = 3;
	const TYPE_SELECT = 4;
	const TYPE_CHECK = 5; // checkbox
	const TYPE_OPTION = 6; // radio
	const TYPE_DATE = 7;
	const TYPE_PHONE = 8;
	const TYPE_PASSWORD = 9;

	const CHECK_PASS = 1;
	const CHECK_FAIL = -1;
	const CHECK_MISSING = -2;

	const DATE_FORMAT_PHP = 1;
	const DATE_FORMAT_DATEPICKER = 2;
	const DATE_FORMAT_MOMENTJS = 3;

	const FORMAT_LANGUAGE_IT = 'it';
	const FORMAT_LANGUAGE_EN = 'en';

	private static $DATE_FORMAT = array(
		self::DATE_FORMAT_PHP => array(
			self::FORMAT_LANGUAGE_IT => 'd/m/Y',
			self::FORMAT_LANGUAGE_EN => 'm/d/Y'
		),
		self::DATE_FORMAT_DATEPICKER => array(
			self::FORMAT_LANGUAGE_IT => 'dd/mm/yy',
			self::FORMAT_LANGUAGE_EN => 'mm/dd/yy'
		),
		self::DATE_FORMAT_MOMENTJS => array(
			self::FORMAT_LANGUAGE_IT => 'DD/MM/YYYY',
			self::FORMAT_LANGUAGE_EN => 'MM/DD/YYYY'
		),
	);

	private $id = NULL;
	private $name = NULL;
	private $type = NULL;
	private $label = NULL;
	private $value = NULL;
	private $style = 'default';

	private $attributes = array(
		'required' => 0,
		'min_size' => false,
		'max_size' => false,
		'size' => false
	);

	public function __construct($name, $type, $label, $attributes)
	{
		$this->id = uniqid();
		$this->name = $name;
		$this->type = $type;
		$this->label = $label;
		$this->attributes = array_merge($this->attributes, $attributes);
	}

	public function check($value)
	{
		$this->value = $value;
		if ($this->attributes['required'] && $value == null) return self::CHECK_MISSING;

		if ($value != null)
		{
			switch($this->type)
			{
				case self::TYPE_PASSWORD:
				case self::TYPE_TEXT:
				{
					if ($this->attributes['min_size'] && strlen($value) < $this->attributes['min_size']) return self::CHECK_FAIL;
					if ($this->attributes['max_size'] && strlen($value) > $this->attributes['max_size']) return self::CHECK_FAIL;
					if ($this->attributes['size'] && strlen($value) != $this->attributes['size']) return self::CHECK_FAIL;
					break;
				}
				case self::TYPE_EMAIL:
				{
					if ($this->attributes['min_size'] && strlen($value) < $this->attributes['min_size']) return self::CHECK_FAIL;
					if ($this->attributes['max_size'] && strlen($value) > $this->attributes['max_size']) return self::CHECK_FAIL;
					if ($this->attributes['size'] && strlen($value) != $this->attributes['size']) return self::CHECK_FAIL;
					$email_regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
					if (!preg_match($email_regex, $value)) return self::CHECK_FAIL;
					break;
				}
				case self::TYPE_NUMBER:
				{
					if ($this->attributes['min_size'] && strlen($value) < $this->attributes['min_size']) return self::CHECK_FAIL;
					if ($this->attributes['max_size'] && strlen($value) > $this->attributes['max_size']) return self::CHECK_FAIL;
					if ($this->attributes['size'] && strlen($value) != $this->attributes['size']) return self::CHECK_FAIL;
					if (!is_numeric($value)) return self::CHECK_FAIL;
					break;
				}
				case self::TYPE_PHONE:
				{
					if ($this->attributes['min_size'] && strlen($value) < $this->attributes['min_size']) return self::CHECK_FAIL;
					if ($this->attributes['max_size'] && strlen($value) > $this->attributes['max_size']) return self::CHECK_FAIL;
					if ($this->attributes['size'] && strlen($value) != $this->attributes['size']) return self::CHECK_FAIL;
					$phone_regex = '/^\+?[0-9\- ]{5,}$/';
					if (!preg_match($phone_regex, $value)) return self::CHECK_FAIL;
					break;
				}
				case self::TYPE_DATE:
				{
					try
					{
						$date = DateTime::createFromFormat($this->getDateFormat(self::DATE_FORMAT_PHP), $value);
						if (!$date) return self::CHECK_FAIL;
					}
					catch (Exception $e)
					{
						return self::CHECK_FAIL;
					}
					break;
				}
				case self::TYPE_SELECT:
				case self::TYPE_CHECK:
				case self::TYPE_OPTION:
			}
		}

		return self::CHECK_PASS;
	}

	public function isRequired($value = null)
	{
		if ($value !== null) $this->attributes['required'] = (boolean)($value);
		return $this->attributes['required'];
	}

	public function isMultiline($value = null)
	{
		if ($value !== null) $this->attributes['multiline'] = (boolean)($value);
		return isset($this->attributes['multiline']) ? $this->attributes['multiline'] : false;
	}

	public function name($value = null)
	{
		if ($value !== null) $this->name = $value;
		return $this->name;
	}

	public function label($value = null)
	{
		if ($value !== null) $this->label = $value;
		return $this->label;
	}

	public function value($value = null)
	{
		if ($value !== null) $this->value = $value;
		return $this->value;
	}

	public function style($value = null)
	{
		if ($value !== null) $this->style = $value;
		return $this->style;
	}

	public function attribute($name, $value = null)
	{
		if ($value !== null) $this->attributes[$name] = $value;
		return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
	}

	public function id()
	{
		return $this->id.'_'.$this->name;
	}

	public function emptyValue()
	{
		$this->value = null;
	}

	public function type($value = null)
	{
		switch($value)
		{
			case self::TYPE_TEXT:
			case self::TYPE_EMAIL:
			case self::TYPE_NUMBER:
			case self::TYPE_SELECT:
			case self::TYPE_CHECK:
			case self::TYPE_OPTION:
			case self::TYPE_DATE:
			case self::TYPE_PHONE:
			case self::TYPE_PASSWORD:
				$this->type = $value;
		}

		return $this->type;
	}

	public function getDateFormat($type)
	{
		if (isset($this->attributes['delimiter']))
			return str_replace('/', $this->attributes['delimiter'], self::$DATE_FORMAT[$type][$this->attributes['format']]);

		return self::$DATE_FORMAT[$type][$this->attributes['format']];
	}

	public function getHTML()
	{
		$smarty = new Smarty();
		$smarty->template_dir 	= realpath(dirname(__FILE__).'/../tpl');
		$smarty->compile_dir 	= realpath(dirname(__FILE__).'/../compiled');
		$smarty->assign('field', $this);

		if (!file_exists(dirname(__FILE__)."/AraFormStyles/{$this->style}.html"))
			$this->style = 'default';

		return $smarty->fetch(dirname(__FILE__)."/AraFormStyles/{$this->style}.html");
	}
}

?>