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
//using only globals
if($toggler->enabled('feature1')) { //true
	...
}

or
//using globals and local context
if($toggler->enabled('feature1', $context)) { //true
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
				['field' => 'site', 'operator' => 'in', 'arg' => ['sk', 'cz']],
				['field' => 'userId', 'operator' => '>', 'arg' => 140]
		],
		'paypal' => [
			'conditions' => [
				['field' => 'site', 'operator' => 'in', 'arg' => ['sk', 'cz', 'de', 'at']]
			]
		]
];
````

or you can use use shorthand syntax for conditions, it's much cleaner and more readable
````
['site', 'in', ['sk', 'cz', 'de', 'at']]
````

Operators:

there's 5 built-in operators, that cannot be overriden:
	> - value must be greater than arg
	< - value must be lower than arg
	= - value must be equal than arg
	in - value must be in set of args
	notIn - value must not be in set of args

You can also register custom operators. A custom operator must implement IOperator interface
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