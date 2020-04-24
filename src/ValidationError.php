<?php

namespace Validator;

class ValidationError
{
    /**
     * @var string
     */
    private static $defaultFormat = self::FORMAT_MESSAGES;
    private $key;
	private $rule;
	private $attributes;

    public const FORMAT_MESSAGES = "messages";
    public const FORMAT_KEYS_WITH_MESSAGES = "keys_with_messages";
    public const FORMAT_WITH_KEYS = self::FORMAT_KEYS_WITH_MESSAGES;
    public const FORMAT_ARRAY = "array";

	public function __construct(string $key, string $rule, $attributes = [])
	{
		$this->key = $key;
		$this->rule = $rule;
		$this->attributes = $attributes;
	}

    public static function setDefaultFormat(string $format): void
    {
        self::$defaultFormat = $format;
    }

    public static function getDefaultFormat(): string
    {
        return self::$defaultFormat;
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
