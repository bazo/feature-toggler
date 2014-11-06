feature-toggler
===============

Feature toggling library for php

usage:

````
$toggler = new Toggler($config);
if($toggler->enabled('featureName')) {
	...
}
````

you can also pass context to toggler:

````
$toggler = new Toggler($config);

$context = [
	'userId' => 150,
	'site' => 'sk'

];
if($toggler->enabled('feature1')) { //true
	...
}
````

configuration:

simple, with an array

````
$config = [
		'globals' => [
			'site'=> 'sk'
		],
		'feature1' => [
			'conditions' => [
				['field' => 'site', 'operator' => 'in', 'arg' => ['sk', 'cz']]
				['field' => 'userId', 'operator' => '>', 'arg' => 140]
		],
		'paypal' => [
			'conditions' => [
				['field' => 'site', 'operator' => 'in', 'arg' => ['sk', 'cz', 'de', 'at']]
			]
		]
];
````

Operators:

there's 4 built-in operators:
	> - value must be greater than arg
	< - value must be lower than arg
	= - value must be equal than arg
	in - value must be in set of args

Then you can register custom operators. A custom operator must implement IOperator interface
````
$operator = new MyCustomOperator;
$toggler->registerOperator($operator);
````

you can also override the default operator sign

````
$toggler->registerOperator($operator, 'myCustomSign');
````

then you write a condition like this

````
['field' => 'site', 'operator' => 'myCustomSign', 'arg' => [1, 2, 3, ...]]
````

Custom features backend:

You can also a custom backend for storing features and their conditions, for example a database

The backend needs to implement **IFeaturesBackend** interface which has one method: **getConfig()**

then you use it like this

````
$backend = new MyRedisBackend(...);
$toggler = new BackendDrivenToggler($backend)
````

enjoy!