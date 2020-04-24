# Validator

![Continuous integration](https://github.com/lefuturiste/validator/workflows/Continuous%20integration/badge.svg)

Simple php validator helper for PSR7 request.

## How to use ?

### From PSR-7

```php
$validator = new Validator($request->getParsedBody());
```

### From php input 

```php
$validator = new Validator($_POST);
```

### Validate methods (or rules)

```php
$validator->required('example');
$validator->notEmpty('example');
```

And more validate methods (or rules)...

### Known if the input is valid

```php
$validator->isValid(); // TRUE|FALSE
```

### Get errors 

To get the list of all the errors that your input have you can use the getErrors() method which return all the errors as an array:

```php
$validator->getErrors();
```

You can get errors in a different format, with the rules as key:

```php
$validator->getErrors(\Validator\ValidationError::FORMAT_WITH_KEYS);
```

Or as a array of array, with each array representing an error with the key 'code' and 'message' (format to use in an JSON:API compliant API):

```php
$validator->getErrors(\Validator\ValidationError::FORMAT_ARRAY);
```

If you dont' want to specify each time the ValidationError format you can use this static call to set as a setting for the whole project.
For example if you want to set the FORMAT_ARRAY as the default format for the whole project you can use this piece of code:

```php
\Validator\ValidationError::setDefaultFormat(\Validator\ValidationError::FORMAT_ARRAY);
```

### I18n

English, french and spanish are supported

```php
ValidationLanguage::setLang('fr'); // or `en` or `es`
```

## Tests

All the tests are in the `tests` folder. You can run tests with theses commands (do a composer install before).

- With composer: `composer run test` or `composer run tests`
- On linux/mac: `vendor/bin/phpunit tests`
- On windows: `vendor/bin/phpunit.bat tests`
