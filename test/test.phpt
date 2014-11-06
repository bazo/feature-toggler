<?php

use Tester\Assert;
use Bazo\FeatureToggler\Toggler;

require __DIR__ . '/../vendor/autoload.php';

class TogglerTest extends \Tester\TestCase
{

	private $config = [
		'globals'	 => [
			'environment' => 'test'
		],
		'feature1'	 => [
			'conditions' => [
				['field' => 'environment', 'operator' => 'in', 'arg' => ['test', 'staging']],
				['field' => 'userId', 'operator' => '>', 'arg' => 140]
			]
		],
		'feature2'	 => [
			'conditions' => [
				['field' => 'environment', 'operator' => '=', 'arg' => 'test']
			]
		]
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

	public function testFeature2()
	{
		Assert::true($this->toggler->enabled('feature2', ['userId' => 150]));
		Assert::true($this->toggler->enabled('feature2', ['userId' => 140, 'environment' => 'test']));
		Assert::false($this->toggler->enabled('feature2', ['userId' => 150, 'environment' => 'production']));
	}


}

$testCase = new TogglerTest;
$testCase->run();
