<?php

namespace Bazo\FeatureToggler;


/**
 * @author Martin Bažík <martin@bazo.sk>
 */
interface IOperator
{

	/**
	 * @return string
	 */
	public function getOperatorSign();

	/**
	 * @param mixed $value
	 * @param array $context
	 * @param mixed|NULL $arg
	 */
	public function evaluateCondition($value, array $context = [], $arg = NULL);
}
