<?php
namespace Sellastica\DataGrid\Mapping\MongoDB;

/**
 * @property \Sellastica\MongoDB\Mapping\MongoMapper $mapper
 */
trait TFilterRulesDao
{
	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @param \Sellastica\Entity\Configuration $configuration
	 * @return \Sellastica\Entity\Entity\EntityCollection
	 */
	public function findByFilterRules(
		\Sellastica\DataGrid\Model\FilterRuleCollection $rules,
		\Sellastica\Entity\Configuration $configuration
	): \Sellastica\Entity\Entity\EntityCollection
	{
		$documents = $this->mapper->findByFilterRules($rules, $configuration);
		return $this->createCollection($documents);
	}
}
