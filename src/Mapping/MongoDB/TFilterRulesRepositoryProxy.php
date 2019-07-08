<?php
namespace Sellastica\DataGrid\Mapping\MongoDB;

/**
 * @method \Sellastica\Entity\Mapping\IRepository|TFilterRulesRepository getRepository
 */
trait TFilterRulesRepositoryProxy
{
	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @param \Sellastica\Entity\Configuration|null $configuration
	 * @return \Sellastica\Entity\Entity\EntityCollection
	 */
	public function findByFilterRules(
		\Sellastica\DataGrid\Model\FilterRuleCollection $rules,
		\Sellastica\Entity\Configuration $configuration = null
	): \Sellastica\Entity\Entity\EntityCollection
	{
		return $this->getRepository()->findByFilterRules($rules, $configuration);
	}

	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @return int
	 */
	public function findCountByFilterRules(
		\Sellastica\DataGrid\Model\FilterRuleCollection $rules
	): int
	{
		return $this->getRepository()->findCountByFilterRules($rules);
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

	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @param \Sellastica\Entity\Configuration|null $configuration
	 */
	public function deleteByFilterRules(
		\Sellastica\DataGrid\Model\FilterRuleCollection $rules,
		\Sellastica\Entity\Configuration $configuration = null
	): void
	{
		$this->getRepository()->deleteByFilterRules($rules, $configuration);
	}
}
