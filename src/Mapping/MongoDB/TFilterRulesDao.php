<?php
namespace Sellastica\DataGrid\Mapping\MongoDB;

/**
 * @property \Sellastica\MongoDB\Mapping\MongoMapper|TFilterRulesMongoMapper $mapper
 */
trait TFilterRulesDao
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
		$documents = $this->mapper->findByFilterRules($rules, $configuration);
		return $this->createCollection($documents);
	}

	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @return int
	 */
	public function findCountByFilterRules(
		\Sellastica\DataGrid\Model\FilterRuleCollection $rules
	): int
	{
		return $this->mapper->findCountByFilterRules($rules);
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
		$this->mapper->updateByFilterRules($rules, $data);
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
		$this->mapper->deleteByFilterRules($rules, $configuration);
	}
}
