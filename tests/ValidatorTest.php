<?php

use PHPUnit\Framework\TestCase;
use Validator\ValidationError;
use Validator\ValidationLanguage;
use Validator\Validator;

class ValidatorTest extends TestCase
{

	private function makeValidator(array $params): Validator
	{
		ValidationLanguage::setLang('fr');
		return new Validator($params);
	}

	public function testRequiredIfFail()
	{
		$errors = $this->makeValidator(['name' => 'joe'])
			->required('name', 'content')
			->getErrors();
		$this->assertCount(1, $errors);
		$this->assertEquals(['Le champs content est requis'], $errors);
	}

	public function testNotEmpty()
	{
		$errors = $this->makeValidator(['name' => 'joe', 'content' => ''])
			->notEmpty('content', 'hello')
			->getErrors();
		$this->assertCount(1, $errors);
	}

	public function testRequiredIfSuccess()
	{
		$errors = $this->makeValidator(['name' => 'joe', 'content' => ''])
			->required('name', 'content')
			->getErrors();
		$this->assertCount(0, $errors);
	}

	public function testInteger()
    {
        $errors = $this->makeValidator(['int' => 'joe'])
            ->integer('int', 'random')
            ->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Le champs int doit être un nombre valide', $errors[0]);
    }

    public function testArraySuccess()
    {
        $errors = $this->makeValidator(['foo' => ['hello' => 'world']])
            ->array('foo', 'lol')
            ->getErrors();
        $this->assertCount(0, $errors);
    }

    public function testArrayError()
    {
        $errors = $this->makeValidator(['foo' => 'joe'])
            ->array('foo')
            ->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Le champs foo doit être un tableau', $errors[0]);
    }

    public function testBooleanSuccess()
    {
        $errors = $this->makeValidator(['foo' => false, 'bar' => 0, 'example' => '0', 'second' => 'true'])
            ->boolean('foo', 'bar', 'example', 'second')
            ->getErrors();
        $this->assertCount(0, $errors);
    }

    public function testBooleanError()
    {
        $errors = $this->makeValidator(['foo' => '_false', 'bar' => 5, 'example' => '001', 'second' => 'string'])
            ->boolean('foo', 'bar', 'example', 'second')
            ->getErrors();
        $this->assertCount(4, $errors);
    }

	public function testSlugSuccess()
	{
		$errors = $this->makeValidator(['slug' => 'aze-aze-azeaze34'])
			->slug('slug')
			->getErrors();
		$this->assertCount(0, $errors);
	}

	public function testSlugError()
	{
		$errors = $this->makeValidator([
			'slug'  => 'aze-aze-azeAze34',
			'slug2' => 'aze-aze_azeAze34',
			'slug3' => 'aze--aze-aze'
		])
			->slug('slug')
			->slug('slug2')
			->slug('slug3')
			->getErrors();
		$this->assertCount(3, $errors);
	}

	public function testLength()
	{
		$params = ['slug' => '123456789'];
		$this->assertCount(0, $this->makeValidator($params)->length('slug', 3)->getErrors());
		$errors = $this->makeValidator($params)->length('slug', 12)->getErrors();
		$this->assertCount(1, $errors);
		$this->assertEquals('Le champs slug doit contenir plus de 12 caractères', $errors[0]);
		$this->assertEquals(['Le champs slug doit contenir plus de 12 caractères'], $errors);
		$this->assertCount(1, $this->makeValidator($params)->length('slug', 3, 4)->getErrors());
		$this->assertCount(0, $this->makeValidator($params)->length('slug', 3, 20)->getErrors());
		$this->assertCount(0, $this->makeValidator($params)->length('slug', null, 20)->getErrors());
		$this->assertCount(1, $this->makeValidator($params)->length('slug', null, 8)->getErrors());
	}

	public function testDateTime()
	{
		$this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 11:12:13'])->dateTime('date')->getErrors());
		$this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 00:00:00'])->dateTime('date')->getErrors());
		$this->assertCount(1, $this->makeValidator(['date' => '2012-21-12'])->dateTime('date')->getErrors());
		$this->assertCount(1, $this->makeValidator(['date' => '2013-02-29 11:12:13'])->dateTime('date')->getErrors());
	}

	public function testEmail()
	{
		$this->assertCount(0, $this->makeValidator(['email' => 'mail@example.com'])->email('email')->getErrors());
	}

	public function testEmailFail()
	{
		$this->assertCount(1, $this->makeValidator(['email' => 'mailexample.com'])->email('email')->getErrors());
		$this->assertCount(1, $this->makeValidator(['email' => 'mailom'])->email('email')->getErrors());
	}

	public function testBetween()
	{
		$this->assertCount(0, $this->makeValidator(['int' => 23])->between('int', '22', '24')->getErrors());
		$this->assertCount(0, $this->makeValidator(['int' => 2])->between('int', '1', '3')->getErrors());
		$this->assertCount(0, $this->makeValidator(['int' => 1])->between('int', '1', '2', false)->getErrors());
		$this->assertCount(0, $this->makeValidator(['int' => 1])->between('int', '1', '1', false)->getErrors());
		$this->assertCount(0, $this->makeValidator(['int' => 15])->between('int', '15', '16', false)->getErrors());
	}

	public function testBetweenFail()
	{
		$this->assertCount(1, $this->makeValidator(['int' => 230])->between('int', '22', '24')->getErrors());
		$this->assertCount(1, $this->makeValidator(['int' => 20])->between('int', '1', '3')->getErrors());
		$this->assertCount(1, $this->makeValidator(['int' => 40])->between('int', '1', '2', false)->getErrors());
		$this->assertCount(1, $this->makeValidator(['int' => 10])->between('int', '1', '1', false)->getErrors());
		$this->assertCount(1, $this->makeValidator(['int' => 50])->between('int', '15', '16', false)->getErrors());
	}

    public function testErrorsWithKeys()
    {
        $validator = $this->makeValidator([]);
        $validator->required('foo');
        $this->assertFalse($validator->isValid());
        $this->assertCount(1, $validator->getErrors());
        $this->assertArrayHasKey('foo.required', $validator->getErrors(true));
    }

    public function testErrorsWithKeysAndGlobalConfig()
    {
        $validator = $this->makeValidator(['foo' => '']);
        ValidationError::withKeys();
        $validator->notEmpty('foo');
        $this->assertFalse($validator->isValid());
        $this->assertCount(1, $validator->getErrors());
        $this->assertArrayHasKey('foo.empty', $validator->getErrors());
    }

    public function testValueExists()
    {
        $validator = $this->makeValidator(['foo' => 'bar', 'hello' => 'world']);
        $this->assertTrue($validator->exists('foo'));
        $this->assertTrue($validator->exists('hello'));
        $this->assertFalse($validator->exists('not_defined'));
        $validator = $this->makeValidator([]);
        $this->assertFalse($validator->exists('not_defined'));
    }

    public function testPatternMatch()
    {
        $validator = $this->makeValidator(['good' => 'hello bar', 'bad' => 'really bad']);
        $validator->patternMatch('good', '/bar/m');
        $validator->patternMatch('bad', '/bar/m');
        $this->assertCount(1, $validator->getErrors());
        $this->assertFalse($validator->isValid());

        $validator = $this->makeValidator(['good' => 'hello bar lol', 'bad' => 'really bad']);
        $pattern = '/([[:alnum:]]+) ([[:alnum:]]+) ([[:alnum:]]+)/m';
        $validator->patternMatch('good', $pattern);
        $validator->patternMatch('bad', $pattern);
        $this->assertCount(1, $validator->getErrors());
        $this->assertFalse($validator->isValid());
    }

    public function testAlphaNumerical()
    {
        $validator = $this->makeValidator(['good' => '0HelloWorld1234', 'bad' => '$_helloWorld_$']);
        $validator->alphaNumerical('good', 'bad');
        $this->assertFalse($validator->isValid());
        $this->assertCount(1, $validator->getErrors());
        $this->assertArrayHasKey("bad.alphaNumerical", $validator->getErrors());

        $validator = $this->makeValidator(['good' => 'MayBeItsJustAnotherString', 'another' => '5478Foo123Bared009']);
        $validator->alphaNumerical('good', 'another');
        $this->assertTrue($validator->isValid());
        $this->assertCount(0, $validator->getErrors());
    }
}
