<?php
namespace Sellastica\DataGrid\Entity;

use Sellastica\Entity\IBuilder;
use Sellastica\Entity\TBuilder;

/**
 * @see AdminFilterRule
 */
class AdminFilterRuleBuilder implements IBuilder
{
	use TBuilder;

	/** @var int */
	private $adminFilterId;
	/** @var string */
	private $criterion;
	/** @var string */
	private $value;

	/**
	 * @param int $adminFilterId
	 * @param string $criterion
	 */
	public function __construct(
		int $adminFilterId,
		string $criterion
	)
	{
		$this->adminFilterId = $adminFilterId;
		$this->criterion = $criterion;
	}

	/**
	 * @return int
	 */
	public function getAdminFilterId(): int
	{
		return $this->adminFilterId;
	}

	/**
	 * @return string
	 */
	public function getCriterion(): string
	{
		return $this->criterion;
	}

	/**
	 * @return string
	 */
	public function getValue(): string
	{
		return $this->value;
	}

	/**
	 * @param string $value
	 * @return $this
	 */
	public function value(string $value)
	{
		$this->value = $value;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function generateId(): bool
	{
		return !AdminFilterRule::isIdGeneratedByStorage();
	}

	/**
	 * @return AdminFilterRule
	 */
	public function build(): AdminFilterRule
	{
		return new AdminFilterRule($this);
	}

	/**
	 * @param int $adminFilterId
	 * @param string $criterion
	 * @return self
	 */
	public static function create(
		int $adminFilterId,
		string $criterion
	): self
	{
		return new self($adminFilterId, $criterion);
	}
}