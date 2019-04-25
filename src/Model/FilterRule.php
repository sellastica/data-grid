<?php
namespace Sellastica\DataGrid\Model;

use Sellastica\Utils\Strings;

class FilterRule
{
	const STRING = 'string',
		INT = 'int',
		BOOLEAN = 'bool',
		ENUM = 'enum',
		SET = 'set',
		DATE = 'date';

	const AND = 'and',
		OR = 'or';

	/** @var string */
	private $key;
	/** @var string|null */
	private $mapping;
	/** @var mixed */
	private $value;
	/** @var mixed */
	private $defaultValue;
	/** @var string */
	private $comparator = '=';
	/** @var string|null */
	private $title;
	/** @var string */
	private $type;
	/** @var string */
	private $operationType;
	/** @var \Closure|null */
	private $displayValue;
	/** @var bool */
	private $inTags = true;
	/** @var bool */
	private $isColumnFilter = true;


	/**
	 * @param string $key
	 * @param mixed $value
	 * @param string $type
	 * @param string $operationType
	 */
	public function __construct(
		string $key,
		$value = null,
		string $type = self::STRING,
		string $operationType = self::AND
	)
	{
		$this->assertType($type);
		$this->assertOperationType($operationType);

		$this->key = $key;
		$this->type = $type;
		$this->value = $value;
		$this->operationType = $operationType;

		if (Strings::endsWith($key, '_min')) {
			$this->comparator = '>=';
		} elseif (Strings::endsWith($key, '_max')) {
			$this->comparator = '<=';
		}
	}

	/**
	 * @return string
	 */
	public function getKey(): string
	{
		return $this->key;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getComparator(): string
	{
		return $this->comparator;
	}

	/**
	 * @param string $comparator
	 */
	public function setComparator(string $comparator)
	{
		$this->comparator = $comparator;
	}

	/**
	 * @return string
	 */
	public function getOperationType(): string
	{
		return $this->operationType;
	}

	/**
	 * @param string $operationType
	 * @return bool
	 */
	public function isOperationType(string $operationType)
	{
		return $operationType === $this->operationType;
	}

	/**
	 * @return bool
	 */
	public function isString(): bool
	{
		return $this->type === self::STRING;
	}

	/**
	 * @return string|null
	 */
	public function getMapping(): ?string
	{
		return $this->mapping;
	}

	/**
	 * @param string $mapping
	 * @return $this
	 */
	public function setMapping(string $mapping)
	{
		$this->mapping = $mapping;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getTitle(): ?string
	{
		return $this->title;
	}

	/**
	 * @param string|null $title
	 * @return $this
	 */
	public function setTitle(?string $title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return mixed
	 */
	public function getResolvedValue()
	{
		switch ($this->type) {
			case self::INT:
				return (int)$this->value;
				break;
			case self::BOOLEAN:
				return (bool)$this->value;
				break;
			default:
				return (string)$this->value;
				break;
		}
	}

	/**
	 * @return \Closure|null
	 */
	public function getDisplayValue(): ?\Closure
	{
		return $this->displayValue;
	}

	/**
	 * @return mixed
	 */
	public function display()
	{
		if (isset($this->displayValue)) {
			$value = $this->displayValue;
			return $value($this);
		} else {
			return $this->value;
		}
	}

	/**
	 * @param \Closure $displayValue
	 * @return $this
	 */
	public function setDisplayValue(\Closure $displayValue)
	{
		$this->displayValue = $displayValue;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDefaultValue()
	{
		return $this->defaultValue;
	}

	/**
	 * @param mixed $defaultValue
	 * @return $this
	 */
	public function setDefaultValue($defaultValue)
	{
		$this->defaultValue = $defaultValue;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isDefault(): bool
	{
		return $this->value === $this->defaultValue;
	}

	/**
	 * @return boolean
	 */
	public function isInTags(): bool
	{
		return $this->inTags;
	}

	/**
	 * @return $this
	 */
	public function removeFromTags()
	{
		$this->inTags = false;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isColumnFilter(): bool
	{
		return $this->isColumnFilter;
	}

	/**
	 * @param bool $isColumnFilter
	 * @return $this
	 */
	public function setIsColumnFilter(bool $isColumnFilter)
	{
		$this->isColumnFilter = $isColumnFilter;
		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) $this->value;
	}

	/**
	 * @param string $type
	 * @throws \InvalidArgumentException
	 */
	private function assertType(string $type)
	{
		$rc = new \ReflectionClass(self::class);
		if (!in_array($type, $rc->getConstants())) {
			throw new \InvalidArgumentException(sprintf('Invalid type "%s"', $type));
		}
	}

	/**
	 * @param string $operationType
	 * @throws \InvalidArgumentException
	 */
	private function assertOperationType(string $operationType)
	{
		if (!in_array($operationType, [self::AND, self::OR])) {
			throw new \InvalidArgumentException(sprintf('Invalid operation type "%s"', $operationType));
		}
	}

	/**
	 * @param $key
	 * @return FilterRule
	 */
	public function withKey($key): self
	{
		$criterion = clone $this;
		$criterion->key = $key;
		return $criterion;
	}

	/**
	 * @param $value
	 * @return FilterRule
	 */
	public function withValue($value): self
	{
		$criterion = clone $this;
		$criterion->value = $value;
		return $criterion;
	}
}