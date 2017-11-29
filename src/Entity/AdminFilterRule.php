<?php
namespace Sellastica\DataGrid\Entity;

use Sellastica\Entity\Entity\AbstractEntity;
use Sellastica\Entity\Entity\IAggregateMember;
use Sellastica\Entity\Entity\IAggregateRoot;
use Sellastica\Entity\Entity\TAbstractEntity;

/**
 * @generate-builder
 * @see AdminFilterRuleBuilder
 *
 * @property AdminFilterRuleRelations $relationService
 */
class AdminFilterRule extends AbstractEntity implements IAggregateMember
{
	use TAbstractEntity;

	/** @var int @required */
	private $adminFilterId;
	/** @var string @required */
	private $criterion;
	/** @var string @optional */
	private $value;


	/**
	 * @param AdminFilterRuleBuilder $builder
	 */
	public function __construct(AdminFilterRuleBuilder $builder)
	{
		$this->hydrate($builder);
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
	 * @return array
	 */
	public function toArray(): array
	{
		return array_merge($this->parentToArray(), [
			'adminFilterId' => $this->adminFilterId,
			'criterion' => $this->criterion,
			'value' => $this->value,
		]);
	}

	/**
	 * @return int
	 */
	public function getAggregateId(): int
	{
		return $this->adminFilterId;
	}

	/**
	 * @return string
	 */
	public function getAggregateRootClass(): string
	{
		return AdminFilter::class;
	}

	/**
	 * @return AdminFilter|IAggregateRoot|null
	 */
	public function getAggregateRoot(): ?IAggregateRoot
	{
		return $this->relationService->getAdminFilter();
	}
}