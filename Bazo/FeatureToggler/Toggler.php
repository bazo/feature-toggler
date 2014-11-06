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

	public function __construct(array $config)
	{
		if (isset($config['globals'])) {
			$this->globals = $config['globals'];
			unset($config['globals']);
		}

		$this->features = $config;
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
			return FALSE;
		}

		$featureData = $this->features[$feature];

		if (key_exists('active', $featureData)) {
			return $featureData['active'];
		} elseif (key_exists('conditions', $featureData)) {
			return $this->evaluateConditions($featureData['conditions'], $context);
		}

		return FALSE;
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
			$res = $this->evaluateCondition($condition, $context);

			if ($res === FALSE) {
				return FALSE;
			}
		}

		return TRUE;
	}


	private function evaluateCondition($condition, $context)
	{
		$value = $context[$condition['field']];

		switch ($condition['operator']) {
			case self::OPERATOR_EQUALS:
				$res = $value === $condition['arg'];
				break;

			case self::OPERATOR_INSET:
				$res = in_array($value, $condition['arg']);
				break;

			case self::OPERATOR_GREATER:
				$res = $value > $condition['arg'];
				break;

			case self::OPERATOR_LESS:
				$res = $value < $condition['arg'];
				break;

			default:
				$res = $this->evaluateCustomOperatorCondition($condition['operator'], $value, $context);
				break;
		}

		return $res;
	}


	private function evaluateCustomOperatorCondition($operatorSign, $value, $context)
	{
		if (!array_key_exists($operatorSign, $this->operators)) {
			throw new UnknownOperatorException(sprintf('Operator "%s" is not registered'));
		}

		$operator = $this->operators[$operatorSign];
		return $operator->evaluateCondition($value, $context);
	}


}
