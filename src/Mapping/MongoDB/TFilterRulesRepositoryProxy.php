<?php
namespace Sellastica\DataGrid\Mapping\MongoDB;

/**
 * @method \Sellastica\Entity\Mapping\IRepository|TFilterRulesRepository getRepository
 */
trait TFilterRulesRepositoryProxy
{
	/**
	 * {@inheritDoc}
	 */
	public function findByFilterRules(
		\Sellastica\DataGrid\Model\FilterRuleCollection $rules,
		\Sellastica\Entity\Configuration $configuration
	): \Sellastica\Entity\Entity\EntityCollection
	{
		return $this->getRepository()->findByFilterRules($rules, $configuration);
	}

	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @param array $data
	 */
	public function updateByFilterRules(
		\Sellastica\DataGrid\Model\FilterRuleCollection $rules,
		array $data
	): void
	{
		$this->getRepository()->updateByFilterRules($rules, $data);
	}
}
