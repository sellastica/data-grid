<?php
namespace Sellastica\DataGrid\Mapping\MongoDB;

/**
 * @method \Sellastica\Entity\Mapping\IRepository getRepository
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
}
