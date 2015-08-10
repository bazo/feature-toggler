<?php

namespace Bazo\FeatureToggler;


/**
 * @author Martin Bažík <martin@bazo.sk>
 */
class Toggler
{

	const OPERATOR_INSET	 = 'in';
	const OPERATOR_EQUALS	 = '=';
	const OPERATOR_LESS	 = '<';
	const OPERATOR_GREATER = '>';

	/** @var array */
	private $features = [];

	/** @var array */
	private $globals = [];

	/** @var IOperator[] */
	private $operators = [];

	/** @var array */
	public $onFeatureEvaluated = [];

	public function __construct(array $config)
	{
		if (isset($config['globals'])) {
			$this->globals = $config['globals'];
			unset($config['globals']);
		}

		$this->features = $config;
	}


	public function addGlobals(array $context)
	{
		$this->globals = array_merge($this->globals, $context);

		return $this;
	}


	public function registerOperator(IOperator $operator, $customSign = NULL)
	{
		$sign					 = is_null($customSign) ? $operator->getOperatorSign() : $customSign;
		$this->operators[$sign]	 = $operator;
		return $this;
	}


	public function enabled($feature, $data = [])
	{
		$context = array_merge($this->globals, $data);

		if (!isset($this->features[$feature])) {
			$result = FALSE;
		} else {

			$featureData = $this->features[$feature];

			if (array_key_exists('active', $featureData)) {
				$result = $featureData['active'];
			} elseif (array_key_exists('conditions', $featureData)) {
				$result = $this->evaluateConditions($featureData['conditions'], $context);
			} else {
				$result = FALSE;
			}
		}
		$this->fireCallbacks($feature, $context, $result);
		return $result;
	}


	private function evaluateConditions($conditions, $context)
	{
		if (empty($conditions)) {
			return FALSE;
		}

		if (!is_array($conditions)) {
			throw new \InvalidArgumentException('Conditions must be an array');
		}

		foreach ($conditions as $condition) {
			$result = $this->evaluateCondition($condition, $context);
			if ($result === FALSE) {
				return FALSE;
			}
		}

		return TRUE;
	}


	private function evaluateCondition($condition, $context)
	{
		if (count($condition) === 2) {
			list($field, $operator) = array_values($condition);
			$arg = NULL;
		} else {
			list($field, $operator, $arg) = array_values($condition);
		}

		if (!isset($context[$field])) {
			return FALSE;
		}

		$value = $context[$field];

		switch ($operator) {
			case self::OPERATOR_EQUALS:
				$res = $value === $arg;
				break;

			case self::OPERATOR_INSET:
				$res = in_array($value, $arg);
				break;

			case self::OPERATOR_GREATER:
				$res = $value > $arg;
				break;

			case self::OPERATOR_LESS:
				$res = $value < $arg;
				break;

			default:
				$res = $this->evaluateCustomOperatorCondition($operator, $value, $context, $arg);
				break;
		}

		return $res;
	}


	private function fireCallbacks($feature, $context, $result)
	{
		foreach ($this->onFeatureEvaluated as $callback) {
			$callback($feature, $context, [], $result);
		}
	}


	private function evaluateCustomOperatorCondition($operatorSign, $value, $context, $arg)
	{
		if (!array_key_exists($operatorSign, $this->operators)) {
			throw new UnknownOperatorException(sprintf('Operator "%s" is not registered', $operatorSign));
		}

		$operator = $this->operators[$operatorSign];
		return $operator->evaluateCondition($value, $context, $arg);
	}


	public function getFeatures()
	{
		return $this->features;
	}


}
