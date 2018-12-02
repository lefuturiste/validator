<?php

namespace Validator;

class ValidationError
{
	private $key;
	private $rule;
	private $attributes;
	private static $withKeys = false;

	public function __construct(string $key, string $rule, $attributes = [])
	{
		$this->key = $key;
		$this->rule = $rule;
		$this->attributes = $attributes;
	}

    public static function withKeys(): void
    {
        self::$withKeys = true;
    }

    public static function getWithKeys(): string
    {
        return self::$withKeys;
    }

    public function __toString(): string
	{
		$params = array_merge([
			ValidationLanguage::getMessages()[$this->rule],
			$this->key
		], $this->attributes);

		return (string)call_user_func_array('sprintf', $params);
	}

	public function getRule(): string
    {
        return $this->rule;
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
