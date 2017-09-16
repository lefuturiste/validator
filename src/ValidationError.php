<?php

namespace Validator;

class ValidationError
{
	private $key;
	private $rule;
	private $messages = [
		'required' => 'Le champs %s est requis',
		'empty' => 'Le champs %s ne peut être vide',
		'slug' => 'Le champs %s n\'est pas un slug valid',
		'minLength' => 'Le champs %s doit contenir plus de %d caractères',
		'maxLength' => 'Le champs %s doit contenir moins de %d caractères',
		'betweenLength' => 'Le champs %s doit contenir entre %d et %d caractères',
		'datetime' => 'Le champs %s doit être une date valide (%s)',
		'notEqual' => 'Le champs %s doit être égale au champs %s',
		'email' => 'Le champs %s doit être un email valide',
		'integer' => 'Le champs %s doit être un nombre valide',
		'url' => 'Le champs %s doit être une url valide',
		'match' => 'Le champs %s doit être égale à %s',
	];
	private $attributes;

	public function __construct($key, $rule, $attributes = [])
	{
		$this->key = $key;
		$this->rule = $rule;
		$this->attributes = $attributes;
	}

	public function __toString()
	{
		$params = array_merge([
			$this->messages[$this->rule],
			$this->key
		], $this->attributes);
		return (string)call_user_func_array('sprintf', $params);
	}
}