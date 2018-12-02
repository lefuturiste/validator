# Validator

Simple php validator for PSR7 request

## How to use ?

### From PSR-7

```php
$validator = new Validator($request->getParsedBody());
```

### From php input 

```php
$validator = new Validator($_POST);
```

### Validate

```php
$validator->required('example');
$validator->notEmpty('example');
```

And more validate methods...

### If valid

```php
$validator->isValid(); // TRUE|FALSE
```

### Get errors 

(array)

```php
$validator->getErrors();
```

You can get errors in a different format, with the rules:

```php
$validator->getErrors(true);
```
