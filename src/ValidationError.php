<?php

namespace Validator;

class ValidationError
{
	private $key;
	private $rule;

	private $attributes;

	public function __construct($key, $rule, $attributes = [])
	{
		$this->key = $key;
		$this->rule = $rule;
		$this->attributes = $attributes;

		//chose the language
	}

	public function __toString()
	{
		$params = array_merge([
			ValidationLanguage::getMessages()[$this->rule],
			$this->key
		], $this->attributes);

		return (string)call_user_func_array('sprintf', $params);
	}
}