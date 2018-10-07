<?php

namespace Validator;

class ValidationError
{
	private $key;
	private $rule;

	private $attributes;

	public function __construct(string $key, string $rule, $attributes = [])
	{
		$this->key = $key;
		$this->rule = $rule;
		$this->attributes = $attributes;
	}

	public function __toString(): string
	{
		$params = array_merge([
			ValidationLanguage::getMessages()[$this->rule],
			$this->key
		], $this->attributes);

		return (string)call_user_func_array('sprintf', $params);
	}
}