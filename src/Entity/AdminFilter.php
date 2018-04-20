<?php
namespace Sellastica\DataGrid\Entity;

use Sellastica\Entity\Entity\AbstractEntity;
use Sellastica\Entity\Entity\IAggregateRoot;
use Sellastica\Entity\Entity\TAbstractEntity;
use Sellastica\Entity\Event\AggregateMemberAdded;

/**
 * @generate-builder
 * @see AdminFilterBuilder
 *
 * @property AdminFilterRelations $relationService
 */
class AdminFilter extends AbstractEntity implements IAggregateRoot
{
	use TAbstractEntity;

	/** @var string @required */
	private $title;
	/** @var string @required */
	private $presenter;
	/** @var AdminFilterRuleCollection|AdminFilterRule[] */
	private $rules;


	/**
	 * @param AdminFilterBuilder $builder
	 */
	public function __construct(AdminFilterBuilder $builder)
	{
		$this->hydrate($builder);
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function getPresenter(): string
	{
		return $this->presenter;
	}

	/**
	 * @return AdminFilterRuleCollection|AdminFilterRule[]
	 */
	public function getRules(): AdminFilterRuleCollection
	{
		$this->initRules();
		return $this->rules;
	}

	/**
	 * @param string $rule
	 * @param string $value
	 */
	public function addCriterion(string $rule, string $value): void
	{
		$this->initRules();
		$this->rules[] = $rule = AdminFilterRuleBuilder::create($this->id, $rule)
			->value($value)
			->build();
		$this->eventPublisher->publish(new AggregateMemberAdded($this, $rule));
	}

	private function initRules(): void
	{
		if (!isset($this->rules)) {
			$this->rules = $this->relationService->getRules();
		}
	}

	/**
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'id' => $this->id,
			'title' => $this->title,
			'presenter' => $this->presenter,
		];
	}
}