<?php

namespace Bazo\FeatureToggler;


/**
 * @author Martin Bažík <martin@bazo.sk>
 */
interface IOperator
{
	public function getOperatorSign();
	public function evaluateCondition($value, $context);
}
