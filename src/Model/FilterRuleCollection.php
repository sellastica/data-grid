<?php
namespace Sellastica\DataGrid\Model;

class FilterRuleCollection implements \IteratorAggregate, \ArrayAccess, \Countable
{
	/** @var array|FilterRule[] */
	private $rules = [];
	/** @var bool */
	private $changed = false;


	/**
	 * @param array|FilterRule[] $rules
	 */
	public function __construct(array $rules = [])
	{
		$this->rules = $rules;
	}

	public function saveState()
	{
		$this->changed = false;
	}

	/**
	 * @return bool
	 */
	public function changed(): bool
	{
		return $this->changed;
	}

	/**
	 * @return \ArrayIterator
	 */
	public function getIterator(): \ArrayIterator
	{
		return new \ArrayIterator($this->rules);
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return FilterRule
	 */
	public function addText(string $key, string $value = null)
	{
		$this->changed = true;
		return $this->rules[$key] = new FilterRule($key, $value, FilterRule::STRING);
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return FilterRule
	 */
	public function addEnum(string $key, string $value = null)
	{
		$this->changed = true;
		return $this->rules[$key] = new FilterRule($key, $value, FilterRule::ENUM);
	}

	/**
	 * @param string $key
	 * @param array $values
	 * @return FilterRule
	 */
	public function addSet(string $key, $values = null)
	{
		$this->changed = true;
		return $this->rules[$key] = new FilterRule($key, (array)$values, FilterRule::SET);
	}

	/**
	 * @param string|null $value
	 * @return FilterRule
	 */
	public function addQuery(string $value = null)
	{
		$this->changed = true;
		$query = $this->rules['q'] = new FilterRule('q', $value, FilterRule::STRING, FilterRule:: OR);
		$query->removeFromTags();
		return $query;
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return FilterRule
	 */
	public function addInt(string $key, string $value = null)
	{
		$this->changed = true;
		return $this->rules[$key] = new FilterRule($key, $value, FilterRule::INT);
	}

	/**
	 * @param FilterRule $criterion
	 */
	public function addRule(FilterRule $criterion)
	{
		$this->changed = true;
		$this->rules[$criterion->getKey()] = $criterion;
	}

	/**
	 * @param null $operationType
	 * @return FilterRuleCollection
	 */
	public function getActiveRules($operationType = null): FilterRuleCollection
	{
		$collection = new self();
		foreach ($this->rules as $key => $criterion) {
			if (!$criterion->isDefault()
				&& (!isset($operationType) || $criterion->isOperationType($operationType))
			) {
				$collection->addRule($criterion);
			}
		}

		return $collection;
	}

	/**
	 * @param array $mapping
	 * @return FilterRuleCollection New instance is returned
	 */
	public function applyMapping(array $mapping)
	{
		$collection = clone $this;
		foreach ($collection->rules as $criterion) {
			if (isset($mapping[$criterion->getKey()])) {
				$criterion->setMapping($mapping[$criterion->getKey()]);
			}
		}

		return $collection;
	}

	/**
	 * Expands one field to more fields with the same attributes and removes the original field
	 * @param string $fieldToExpand
	 * @param array $fields
	 * @return FilterRuleCollection
	 */
	public function expand(string $fieldToExpand, array $fields)
	{
		$this->changed = true;
		$rules = $this->rules;
		if (isset($rules[$fieldToExpand])) {
			$rule = $rules[$fieldToExpand];
			unset($rules[$fieldToExpand]);
			foreach ($fields as $key => $mapping) {
				$rules[$key] = $rule->withKey($key);
				if (isset($mapping)) {
					$rules[$key]->setMapping($mapping);
				}
			}
		}

		return new self($rules);
	}

	/**
	 * @return array
	 */
	public function getKeyValuePairs(): array
	{
		$pairs = [];
		foreach ($this->rules as $key => $rule) {
			$pairs[$key] = $rule->getValue();
		}

		return $pairs;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetExists($offset)
	{
		return isset($this->rules[$offset]);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetGet($offset)
	{
		return isset($this->rules[$offset]) ? $this->rules[$offset] : null;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetSet($offset, $value)
	{
		if (isset($offset)) {
			throw new \Exception('Cannot set rule with defined key');
		} elseif (!$value instanceof FilterRule) {
			throw new \Exception('Rule must be instance of SearchCriterion');
		}

		$this->addRule($value);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetUnset($offset)
	{
		throw new \Exception('Operation not allowed');
	}

	/**
	 * @return int
	 */
	public function count(): int
	{
		return sizeof($this->rules);
	}
}