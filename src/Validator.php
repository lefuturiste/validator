<?php

namespace Validator;

class Validator
{
	/**
	 * @var array
	 */
	private $params;

	/**
	 * @var string[]
	 */
	private $errors = [];

	public function __construct($params)
	{

		$this->params = $params;
	}

	/**
	 * Verifie que les champs sont présent dans le tableau
	 *
	 * @param array ...$keys Les clef à vérifier
	 * @return $this
	 */
	public function required(...$keys)
	{
		foreach ($keys as $key) {
			$value = $this->getValue($key);
			if (is_null($value))
				$this->addError($key, 'required');
		}

		return $this;
	}

	/**
	 * Verifie qu'un champs n'est pas vide
	 *
	 * @param array ...$keys Les clef à vérifier
	 * @return $this
	 */
	public function notEmpty(...$keys)
	{
		foreach ($keys as $key) {
			$value = $this->getValue($key);
			if (is_null($value) || empty($value))
				$this->addError($key, 'empty');
		}

		return $this;
	}

	/**
	 * Verifie qu'un champs à la longueur correcte
	 *
	 * @param $key
	 * @param $min La longueur minimum
	 * @param null $max La longueur maximale
	 * @return $this
	 */
	public function length($key, $min, $max = NULL)
	{
		$value = $this->getValue($key);

		//on ne traite pas le champs si il est vide

		//on ne traite pas le champs si il est vide
		if (!empty($value)) {
			$length = mb_strlen($value);
			if (
				!is_null($min) &&
				!is_null($max) &&
				($length < $min || $length > $max)
			) {
				$this->addError($key, 'betweenLength', [
					$min,
					$max
				]);

			}

			if (
				!is_null($min) &&
				$length < $min
			) {
				$this->addError($key, 'minLength', [
					$min
				]);

			}

			if (
				!is_null($max) &&
				$length > $max
			) {
				$this->addError($key, 'maxLength', [
					$max
				]);

			}
		}

		return $this;
	}

	/**
	 * Vérifie qu'un champs est bien au format dateTime
	 *
	 * @param $key
	 * @param string $format
	 * @return $this
	 */
	public function dateTime($key, $format = 'Y-m-d H:i:s')
	{
		$value = $this->getValue($key);

		//on ne traite pas le champs si il est vide
		if (!empty($value)) {
			$date = \DateTime::createFromFormat($format, $value);
			$errors = \DateTime::getLastErrors();
			if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date == false)
				$this->addError($key, 'datetime', [$format]);
		}

		return $this;
	}

	/**
	 * Verifie qu'un champs est un slug
	 *
	 * @param $key La clef à vérifier
	 * @return $this
	 */
	public function slug($key)
	{
		$value = $this->getValue($key);

		//on ne traite pas le champs si il est vide
		if (!empty($value)) {
			$pattern = '/^([a-z0-9]+-?)+$/';

			if (is_null($value) || !preg_match($pattern, $this->params[$key]))
				$this->addError($key, 'slug');

			return $this;
		}
	}

	/**
	 * Verifie si la clef est une url valide
	 *
	 * @param $key
	 * @return $this
	 */
	public function url($key)
	{
		$value = $this->getValue($key);

		//on ne traite pas le champs si il est vide
		if (!empty($value)) {
			$pattern = '|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i';

			if (is_null($value) || !preg_match($pattern, $this->params[$key]))
				$this->addError($key, 'url');

			return $this;
		}
	}

	/**
	 * Vérifie si une clef match avec une variable
	 *
	 * @param $key La première clef
	 * @param $key1 La deuxième clef
	 * @return $this
	 */
	public function match($key, $varToCompare)
	{
		$value = $this->getValue($key);
		//on ne traite pas le champs si il est vide
		if (!empty($value) && !empty($varToCompare))
			if ($value != $varToCompare)
				$this->addError($key, 'match', [
					$varToCompare
				]);

		return $this;
	}

	/**
	 * Vérifie si une clef est égale à une autrec clef (compare deux clef)
	 *
	 * @param $key La première clef
	 * @param $key1 La deuxième clef
	 * @return $this
	 */
	public function equal($key, $key1)
	{
		$value = $this->getValue($key);
		$value1 = $this->getValue($key1);
		//on ne traite pas le champs si il est vide
		if (!empty($value) && !empty($value1))
			if ($value != $value1)
				$this->addError($key, 'notEqual', [
					$key1
				]);

		return $this;
	}

	/**
	 * Verifie si un email est valide
	 *
	 * @param $key un email à vérifier
	 * @return $this
	 */
	public function email($key)
	{
		$value = $this->getValue($key);
		//on ne traite pas le champs si il est vide
		if (!empty($value)) {
			if (!filter_var($value, FILTER_VALIDATE_EMAIL))
				$this->addError($key, 'email', [
					$key
				]);
		}

		return $this;
	}

	/**
	 * Verifie si une clef est un nombre valide
	 *
	 * @param $key
	 * @return $this
	 */
	public function integer($key)
	{
		$value = $this->getValue($key);
		$pattern = '/^([0-9]+-?)+$/';
		if (is_null($value) || !preg_match($pattern, $this->params[$key]))
			$this->addError($key, 'integer');

		return $this;
	}

	/**
	 * Récupère les erreurs
	 *
	 * @return ValidationError[]
	 */
	public function getErrors()
	{
		$errors = [];
		foreach ($this->errors as $key => $error)
			array_push($errors, (string)$error);

		return $errors;
	}

	/**
	 * Retourne true si il n'y a pas d'erreurs et false si il y des erreurs
	 *
	 * @return bool
	 */
	public function isValid()
	{
		return empty($this->errors);
	}

	private function getValue($key)
	{
		if (array_key_exists($key, $this->params))
			return $this->params[$key];

		return NULL;
	}

	/**
	 * Ajoute une erreur
	 *
	 * @param $key
	 * @param $rule
	 *
	 * @param array $attributes
	 * @return void
	 */
	private function addError($key, $rule, $attributes = [])
	{
		$this->errors[$key] = new ValidationError($key, $rule, $attributes);
	}

}