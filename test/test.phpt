<?php

use Tester\Assert;
use Bazo\FeatureToggler\Toggler;

require __DIR__ . '/../vendor/autoload.php';

class CustomOperator implements Bazo\FeatureToggler\IOperator
{

	public function evaluateCondition($value, array $context = [], $arg = NULL)
	{
		return ($value * 3) % 2 === 0;
	}


	public function getOperatorSign()
	{
		return 'custom';
	}


}

class TogglerTest extends \Tester\TestCase
{

	private $config = [
		'globals' => [
			'environment' => 'test'
		],
		'feature1' => [
			'conditions' => [
				['field' => 'environment', 'operator' => 'in', 'arg' => ['test', 'staging']],
				['field' => 'userId', 'operator' => '>', 'arg' => 140]
			]
		],
		'feature2' => [
			'conditions' => [
				['field' => 'environment', 'operator' => '=', 'arg' => 'test']
			]
		],
		'feature3' => [
			'conditions' => [
				['field' => 'environment', 'operator' => 'unknown', 'arg' => 'test']
			]
		],
		'feature4' => [
			'conditions' => [
				['field' => 'userId', 'operator' => 'custom', 'arg' => 'not mandatory']
			]
		],
		'feature5' => [
			'conditions' => [
				['field' => 'environment', 'operator' => 'notIn', 'arg' => ['test', 'staging']],
			]
		],
	];

	/** @var Toggler */
	private $toggler;

	public function setUp()
	{
		parent::setUp();
		$this->toggler = new \Bazo\FeatureToggler\Toggler($this->config);
	}


	public function testFeature1()
	{
		Assert::true($this->toggler->enabled('feature1', ['userId' => 150]));
		Assert::false($this->toggler->enabled('feature1', ['userId' => 140]));
		Assert::false($this->toggler->enabled('feature1', ['userId' => 150, 'environment' => 'production']));
	}


	public function testFeature3()
	{
		Assert::exception(function() {
			$this->toggler->enabled('feature3', ['userId' => 150]);
		}, \Bazo\FeatureToggler\UnknownOperatorException::class);
	}


	public function testFeature4()
	{
		$operator = new CustomOperator;

		$this->toggler->registerOperator($operator);

		Assert::true($this->toggler->enabled('feature4', ['userId' => 6]));
		Assert::false($this->toggler->enabled('feature4', ['userId' => 5]));
	}


	public function testFeature5()
	{
		Assert::true($this->toggler->enabled('feature5', ['environment' => 'production']));
	}


}

Tester\Environment::setup();

$testCase = new TogglerTest;
$testCase->run();
